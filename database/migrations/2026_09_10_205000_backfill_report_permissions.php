<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $now = now();
        $slug = 'reports.view';
        $attributes = [
            'name' => 'Ver reportes operativos y clínicos',
            'description' => 'Consultar reportes agregados y metadatos operativos autorizados.',
        ];

        $existing = DB::table('permissions')->where('slug', $slug)->exists();

        if ($existing) {
            DB::table('permissions')->where('slug', $slug)->update([
                ...$attributes,
                'updated_at' => $now,
            ]);
        } else {
            DB::table('permissions')->insert([
                'slug' => $slug,
                ...$attributes,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $permissionId = DB::table('permissions')->where('slug', $slug)->value('id');

        foreach (['administrator', 'clinical_coordination', 'consultation_direction'] as $roleSlug) {
            $roleId = DB::table('roles')->where('slug', $roleSlug)->value('id');

            if (! $roleId || ! $permissionId) {
                continue;
            }

            DB::table('permission_role')->insertOrIgnore([
                'permission_id' => $permissionId,
                'role_id' => $roleId,
            ]);
        }
    }

    public function down(): void
    {
        // Authorization backfills are intentionally non-destructive on rollback.
        // Removing grants here could revoke permissions customized after deployment.
    }
};
