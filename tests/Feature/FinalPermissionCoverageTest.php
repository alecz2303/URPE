<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinalPermissionCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_role_permission_matrix_preserves_v1_least_privilege(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $expected = [
            'clinical_coordination' => [
                'therapies.manage', 'patients.view', 'patients.manage', 'clinical_records.view', 'clinical_records.manage',
                'appointments.view', 'appointments.manage', 'session_logs.view', 'session_logs.manage',
                'session_logs.manage_all', 'reports.view',
            ],
            'therapist' => ['session_logs.view', 'session_logs.manage'],
            'reception' => ['patients.view', 'patients.manage', 'appointments.view', 'appointments.manage'],
            'consultation_direction' => ['patients.view', 'appointments.view', 'reports.view'],
        ];

        foreach ($expected as $roleSlug => $permissionSlugs) {
            $actual = Role::query()->where('slug', $roleSlug)->firstOrFail()
                ->permissions()->pluck('slug')->sort()->values()->all();
            sort($permissionSlugs);

            $this->assertSame($permissionSlugs, $actual, "Unexpected baseline permissions for {$roleSlug}.");
        }
    }

    public function test_operational_roles_cannot_bypass_clinical_or_security_surfaces_by_direct_url(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $reception = User::factory()->create();
        $reception->assignRole('reception');

        $this->actingAs($reception)->get('/reportes')->assertForbidden();
        $this->actingAs($reception)->get('/roles')->assertForbidden();
        $this->actingAs($reception)->get('/usuarios')->assertForbidden();

        $direction = User::factory()->create();
        $direction->assignRole('consultation_direction');

        $this->actingAs($direction)->get('/usuarios')->assertForbidden();
        $this->actingAs($direction)->get('/roles')->assertForbidden();
        $this->actingAs($direction)->get('/configuracion/centro')->assertForbidden();
    }

    public function test_therapist_role_does_not_gain_global_operational_or_clinical_permissions(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $therapist = User::factory()->create();
        $therapist->assignRole('therapist');

        foreach ([
            'patients.view',
            'patients.manage',
            'clinical_records.view',
            'clinical_records.manage',
            'appointments.view',
            'appointments.manage',
            'session_logs.manage_all',
            'reports.view',
            'users.view',
            'roles.manage',
        ] as $permission) {
            $this->assertFalse($therapist->hasPermission($permission), "Therapist unexpectedly has {$permission}.");
        }

        $this->assertTrue($therapist->hasPermission('session_logs.view'));
        $this->assertTrue($therapist->hasPermission('session_logs.manage'));
    }
}
