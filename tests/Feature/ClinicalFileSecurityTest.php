<?php

namespace Tests\Feature;

use App\Models\ClinicalFile;
use App\Models\User;
use App\Services\ClinicalFileStorage;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ClinicalFileSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuthorizationSeeder::class);
        Storage::fake('clinical');
    }

    public function test_clinical_disk_is_private_and_outside_public_path(): void
    {
        $root = config('filesystems.disks.clinical.root');

        $this->assertSame(storage_path('app/clinical-private'), $root);
        $this->assertFalse(config('filesystems.disks.clinical.serve'));
        $this->assertSame('private', config('filesystems.disks.clinical.visibility'));
        $this->assertFalse(str_starts_with($root, public_path()));
    }

    public function test_service_stores_file_with_generated_name_metadata_hash_and_audit(): void
    {
        $actor = User::factory()->create();
        $file = UploadedFile::fake()->createWithContent('radiografia.pdf', 'contenido-clinico-prueba');

        $clinicalFile = app(ClinicalFileStorage::class)->store($file, $actor, metadata: ['category' => 'study']);

        Storage::disk('clinical')->assertExists($clinicalFile->path);
        $this->assertSame('clinical', $clinicalFile->disk);
        $this->assertSame('radiografia.pdf', $clinicalFile->original_name);
        $this->assertNotSame('radiografia.pdf', basename($clinicalFile->path));
        $this->assertSame(hash('sha256', 'contenido-clinico-prueba'), $clinicalFile->sha256);
        $this->assertSame($actor->id, $clinicalFile->uploaded_by);
        $this->assertSame(['category' => 'study'], $clinicalFile->metadata);

        $this->assertDatabaseHas('audit_events', [
            'event' => 'clinical_file.stored',
            'target_type' => ClinicalFile::class,
            'target_id' => (string) $clinicalFile->id,
            'actor_id' => $actor->id,
        ]);
    }

    public function test_retiring_file_soft_deletes_metadata_but_retains_physical_bytes_and_audits_action(): void
    {
        $actor = User::factory()->create();
        $file = UploadedFile::fake()->createWithContent('estudio.pdf', 'archivo-a-retener');
        $clinicalFile = app(ClinicalFileStorage::class)->store($file, $actor);

        app(ClinicalFileStorage::class)->retire($clinicalFile, $actor, 'Documento sustituido');

        Storage::disk('clinical')->assertExists($clinicalFile->path);
        $this->assertSoftDeleted('clinical_files', ['id' => $clinicalFile->id]);
        $this->assertDatabaseHas('audit_events', [
            'event' => 'clinical_file.retired',
            'target_type' => ClinicalFile::class,
            'target_id' => (string) $clinicalFile->id,
            'actor_id' => $actor->id,
        ]);
    }

    public function test_guest_cannot_download_clinical_file(): void
    {
        $clinicalFile = $this->storedClinicalFile();

        $this->get(route('clinical-files.download', $clinicalFile))
            ->assertRedirect(route('login'));
    }

    public function test_user_without_permission_cannot_download_clinical_file(): void
    {
        $clinicalFile = $this->storedClinicalFile();

        $this->actingAs(User::factory()->create())
            ->get(route('clinical-files.download', $clinicalFile))
            ->assertForbidden();
    }

    public function test_authorized_user_can_download_existing_private_file_and_access_is_audited(): void
    {
        $clinicalFile = $this->storedClinicalFile();
        $user = User::factory()->create();
        $user->assignRole('administrator');

        $response = $this->actingAs($user)
            ->get(route('clinical-files.download', $clinicalFile));

        $response->assertOk();
        $response->assertDownload($clinicalFile->original_name);

        $this->assertDatabaseHas('audit_events', [
            'event' => 'clinical_file.downloaded',
            'target_type' => ClinicalFile::class,
            'target_id' => (string) $clinicalFile->id,
            'actor_id' => $user->id,
        ]);
    }

    public function test_authorized_download_returns_not_found_when_physical_file_is_missing(): void
    {
        $clinicalFile = ClinicalFile::query()->create([
            'uuid' => (string) Str::uuid(),
            'disk' => 'clinical',
            'path' => 'documents/missing.pdf',
            'original_name' => 'missing.pdf',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'size_bytes' => 100,
            'sha256' => str_repeat('a', 64),
        ]);
        $user = User::factory()->create();
        $user->assignRole('administrator');

        $this->actingAs($user)
            ->get(route('clinical-files.download', $clinicalFile))
            ->assertNotFound();

        $this->assertDatabaseMissing('audit_events', [
            'event' => 'clinical_file.downloaded',
            'target_id' => (string) $clinicalFile->id,
        ]);
    }

    private function storedClinicalFile(): ClinicalFile
    {
        $path = 'documents/'.Str::uuid().'.pdf';
        Storage::disk('clinical')->put($path, 'contenido-clinico');

        return ClinicalFile::query()->create([
            'uuid' => (string) Str::uuid(),
            'disk' => 'clinical',
            'path' => $path,
            'original_name' => 'estudio.pdf',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'size_bytes' => strlen('contenido-clinico'),
            'sha256' => hash('sha256', 'contenido-clinico'),
        ]);
    }
}
