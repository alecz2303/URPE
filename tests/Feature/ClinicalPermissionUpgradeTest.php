<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicalPermissionUpgradeTest extends TestCase
{
    use RefreshDatabase;

    public function test_upgrade_backfills_clinical_session_permissions_without_removing_existing_role_grants(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $customPermission = Permission::query()->create([
            'name' => 'Permiso personalizado',
            'slug' => 'custom.keep',
            'description' => 'Debe conservarse durante el upgrade.',
        ]);

        $therapist = Role::query()->where('slug', 'therapist')->firstOrFail();
        $therapist->permissions()->attach($customPermission->id);

        Permission::query()
            ->whereIn('slug', ['session_logs.view', 'session_logs.manage', 'session_logs.manage_all'])
            ->delete();

        $migration = require database_path('migrations/2026_09_10_040000_backfill_clinical_session_permissions.php');
        $migration->up();

        $this->assertDatabaseHas('permissions', ['slug' => 'session_logs.view']);
        $this->assertDatabaseHas('permissions', ['slug' => 'session_logs.manage']);
        $this->assertDatabaseHas('permissions', ['slug' => 'session_logs.manage_all']);

        $therapist->refresh();
        $this->assertTrue($therapist->permissions()->where('slug', 'custom.keep')->exists());
        $this->assertTrue($therapist->permissions()->where('slug', 'session_logs.view')->exists());
        $this->assertTrue($therapist->permissions()->where('slug', 'session_logs.manage')->exists());
        $this->assertFalse($therapist->permissions()->where('slug', 'session_logs.manage_all')->exists());

        $administrator = Role::query()->where('slug', 'administrator')->firstOrFail();
        $coordination = Role::query()->where('slug', 'clinical_coordination')->firstOrFail();

        foreach ([$administrator, $coordination] as $role) {
            $this->assertTrue($role->permissions()->where('slug', 'session_logs.view')->exists());
            $this->assertTrue($role->permissions()->where('slug', 'session_logs.manage')->exists());
            $this->assertTrue($role->permissions()->where('slug', 'session_logs.manage_all')->exists());
        }
    }
}
