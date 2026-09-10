<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $now = now();
        $permissions = [
            'session_logs.view' => [
                'name' => 'Ver bitácoras clínicas asignadas',
                'description' => 'Consultar bitácoras de sesiones clínicas autorizadas.',
            ],
            'session_logs.manage' => [
                'name' => 'Capturar bitácoras clínicas asignadas',
                'description' => 'Crear y actualizar bitácoras de sesiones clínicas autorizadas.',
            ],
            'session_logs.manage_all' => [
                'name' => 'Administrar todas las bitácoras clínicas',
                'description' => 'Consultar y administrar bitácoras clínicas sin restricción por asignación de terapeuta.',
            ],
        ];

        foreach ($permissions as $slug => $attributes) {
            $existing = DB::table('permissions')->where('slug', $slug)->exists();

            if ($existing) {
                DB::table('permissions')->where('slug', $slug)->update([
                    ...$attributes,
                    'updated_at' => $now,
                ]);

                continue;
            }

            DB::table('permissions')->insert([
                'slug' => $slug,
                ...$attributes,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $permissionIds = DB::table('permissions')
            ->whereIn('slug', array_keys($permissions))
            ->pluck('id', 'slug');

        $baseline = [
            'administrator' => ['session_logs.view', 'session_logs.manage', 'session_logs.manage_all'],
            'clinical_coordination' => ['session_logs.view', 'session_logs.manage', 'session_logs.manage_all'],
            'therapist' => ['session_logs.view', 'session_logs.manage'],
        ];

        foreach ($baseline as $roleSlug => $permissionSlugs) {
            $roleId = DB::table('roles')->where('slug', $roleSlug)->value('id');
            if (! $roleId) {
                continue;
            }

            foreach ($permissionSlugs as $permissionSlug) {
                $permissionId = $permissionIds->get($permissionSlug);
                if (! $permissionId) {
                    continue;
                }

                DB::table('permission_role')->insertOrIgnore([
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Authorization backfills are intentionally non-destructive on rollback.
        // Removing grants here could revoke permissions customized after deployment.
    }
};
