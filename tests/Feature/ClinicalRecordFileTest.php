<?php

namespace Tests\Feature;

use App\Models\ClinicalFile;
use App\Models\Patient;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClinicalRecordFileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuthorizationSeeder::class);
        Storage::fake('clinical');
    }

    public function test_clinical_coordination_can_upload_file_linked_to_patient_record(): void
    {
        $patient = $this->patient();
        $record = $patient->clinicalRecord()->create(['diagnoses' => 'Base']);
        $user = $this->coordinationUser();

        $response = $this->actingAs($user)->post(route('clinical-record-files.store', $patient), [
            'file' => UploadedFile::fake()->createWithContent('radiografia.pdf', 'radiografia-control'),
            'category' => 'radiograph',
            'description' => 'Control anual',
        ]);

        $response->assertRedirect(route('clinical-records.show', $patient))
            ->assertSessionHas('status', 'Archivo clínico cargado correctamente.');

        $clinicalFile = ClinicalFile::query()->firstOrFail();
        $this->assertSame($record->getMorphClass(), $clinicalFile->subject_type);
        $this->assertSame((string) $record->id, (string) $clinicalFile->subject_id);
        $this->assertSame('radiograph', $clinicalFile->metadata['category']);
        $this->assertSame('Control anual', $clinicalFile->metadata['description']);
        Storage::disk('clinical')->assertExists($clinicalFile->path);
    }

    public function test_upload_requires_existing_record_and_valid_file_type(): void
    {
        $patient = $this->patient();
        $user = $this->coordinationUser();

        $this->actingAs($user)
            ->post(route('clinical-record-files.store', $patient), [
                'file' => UploadedFile::fake()->create('archivo.exe', 10, 'application/octet-stream'),
                'category' => 'document',
            ])
            ->assertNotFound();

        $patient->clinicalRecord()->create(['diagnoses' => 'Base']);

        $this->actingAs($user)
            ->from(route('clinical-records.show', $patient))
            ->post(route('clinical-record-files.store', $patient), [
                'file' => UploadedFile::fake()->create('archivo.exe', 10, 'application/octet-stream'),
                'category' => 'document',
            ])
            ->assertRedirect(route('clinical-records.show', $patient))
            ->assertSessionHasErrors('file');

        $this->assertDatabaseCount('clinical_files', 0);
    }

    public function test_record_view_lists_only_active_files_for_that_record(): void
    {
        $patient = $this->patient('Alan');
        $otherPatient = $this->patient('Bruno');
        $record = $patient->clinicalRecord()->create(['diagnoses' => 'Base']);
        $otherRecord = $otherPatient->clinicalRecord()->create(['diagnoses' => 'Otra base']);
        $user = $this->coordinationUser();

        $visible = $this->storeForRecord($record, $user, 'visible.pdf');
        $retired = $this->storeForRecord($record, $user, 'retirado.pdf');
        $this->storeForRecord($otherRecord, $user, 'otro-paciente.pdf');
        $retired->delete();

        $this->actingAs($user)
            ->get(route('clinical-records.show', $patient))
            ->assertOk()
            ->assertSee($visible->original_name)
            ->assertDontSee('retirado.pdf')
            ->assertDontSee('otro-paciente.pdf');
    }

    public function test_record_view_permission_can_download_linked_file_without_global_download_permission(): void
    {
        $patient = $this->patient();
        $record = $patient->clinicalRecord()->create(['diagnoses' => 'Base']);
        $coordination = $this->coordinationUser();
        $clinicalFile = $this->storeForRecord($record, $coordination, 'estudio.pdf');

        $this->assertFalse($coordination->can('clinical_files.download'));
        $this->assertTrue($coordination->can('clinical_records.view'));

        $this->actingAs($coordination)
            ->get(route('clinical-files.download', $clinicalFile))
            ->assertOk()
            ->assertDownload('estudio.pdf');
    }

    public function test_user_without_record_view_cannot_download_linked_file_even_if_uuid_is_known(): void
    {
        $patient = $this->patient();
        $record = $patient->clinicalRecord()->create(['diagnoses' => 'Base']);
        $coordination = $this->coordinationUser();
        $clinicalFile = $this->storeForRecord($record, $coordination, 'privado.pdf');
        $therapist = User::factory()->create();
        $therapist->assignRole('therapist');

        $this->actingAs($therapist)
            ->get(route('clinical-files.download', $clinicalFile))
            ->assertForbidden();
    }

    public function test_manage_permission_can_retire_file_without_deleting_physical_bytes(): void
    {
        $patient = $this->patient();
        $record = $patient->clinicalRecord()->create(['diagnoses' => 'Base']);
        $user = $this->coordinationUser();
        $clinicalFile = $this->storeForRecord($record, $user, 'estudio.pdf');

        $this->actingAs($user)
            ->delete(route('clinical-record-files.destroy', [$patient, $clinicalFile]))
            ->assertRedirect(route('clinical-records.show', $patient))
            ->assertSessionHas('status', 'Archivo clínico retirado del expediente.');

        $this->assertSoftDeleted('clinical_files', ['id' => $clinicalFile->id]);
        Storage::disk('clinical')->assertExists($clinicalFile->path);
        $this->assertDatabaseHas('audit_events', [
            'event' => 'clinical_file.retired',
            'target_type' => ClinicalFile::class,
            'target_id' => (string) $clinicalFile->id,
            'actor_id' => $user->id,
        ]);
    }

    public function test_file_cannot_be_retired_through_another_patient_record(): void
    {
        $patient = $this->patient('Alan');
        $otherPatient = $this->patient('Bruno');
        $record = $patient->clinicalRecord()->create(['diagnoses' => 'Base']);
        $otherPatient->clinicalRecord()->create(['diagnoses' => 'Otra base']);
        $user = $this->coordinationUser();
        $clinicalFile = $this->storeForRecord($record, $user, 'aislado.pdf');

        $this->actingAs($user)
            ->delete(route('clinical-record-files.destroy', [$otherPatient, $clinicalFile]))
            ->assertNotFound();

        $this->assertDatabaseHas('clinical_files', [
            'id' => $clinicalFile->id,
            'deleted_at' => null,
        ]);
    }

    private function patient(string $firstName = 'Alan'): Patient
    {
        return Patient::query()->create([
            'first_name' => $firstName,
            'last_name' => 'Ramírez',
            'date_of_birth' => '2017-02-13',
        ]);
    }

    private function coordinationUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('clinical_coordination');

        return $user;
    }

    private function storeForRecord($record, User $user, string $name): ClinicalFile
    {
        return app(\App\Services\ClinicalFileStorage::class)->store(
            UploadedFile::fake()->createWithContent($name, 'contenido-'.$name),
            $user,
            $record,
            ['category' => 'study'],
        );
    }
}
