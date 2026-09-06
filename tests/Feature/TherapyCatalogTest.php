<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Therapy;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Database\Seeders\TherapySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TherapyCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_initial_therapies_are_seeded_as_configurable_data(): void
    {
        $this->seed(TherapySeeder::class);

        $this->assertDatabaseHas('therapies', [
            'name' => 'Vojta',
            'duration_minutes' => 40,
            'required_therapists' => 1,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('therapies', [
            'name' => 'Pediasuit',
            'duration_minutes' => 60,
            'required_therapists' => 2,
            'is_active' => true,
        ]);
    }

    public function test_therapy_model_casts_scheduling_attributes(): void
    {
        $therapy = Therapy::query()->create([
            'name' => 'Terapia de prueba',
            'duration_minutes' => 45,
            'required_therapists' => 2,
            'color' => '#123ABC',
            'is_active' => true,
        ]);

        $therapy->refresh();

        $this->assertSame(45, $therapy->duration_minutes);
        $this->assertSame(2, $therapy->required_therapists);
        $this->assertTrue($therapy->is_active);
    }

    public function test_authorization_seeder_creates_therapy_management_permission(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $this->assertDatabaseHas('permissions', ['slug' => 'therapies.manage']);

        $administrator = Role::query()->where('slug', 'administrator')->firstOrFail();
        $this->assertTrue($administrator->permissions()->where('slug', 'therapies.manage')->exists());
    }

    public function test_clinical_coordination_receives_therapy_management_permission(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $coordination = Role::query()->where('slug', 'clinical_coordination')->firstOrFail();

        $this->assertTrue($coordination->permissions()->where('slug', 'therapies.manage')->exists());
    }

    public function test_therapist_role_does_not_receive_therapy_management_permission_by_default(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $therapist = Role::query()->where('slug', 'therapist')->firstOrFail();

        $this->assertFalse($therapist->permissions()->where('slug', 'therapies.manage')->exists());
    }
}
