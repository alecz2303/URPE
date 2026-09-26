<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            ['name' => 'Consultar evaluaciones clínicas', 'slug' => 'clinical_assessments.view', 'description' => 'Permite consultar evaluaciones clínicas del paciente.'],
            ['name' => 'Gestionar evaluaciones clínicas', 'slug' => 'clinical_assessments.manage', 'description' => 'Permite crear, editar borradores y finalizar evaluaciones clínicas.'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $permission['slug']],
                [...$permission, 'created_at' => now(), 'updated_at' => now()],
            );
        }

        $permissionIds = DB::table('permissions')
            ->whereIn('slug', array_column($permissions, 'slug'))
            ->pluck('id');

        $roleIds = DB::table('roles')
            ->whereIn('slug', ['administrator', 'clinical_coordination'])
            ->pluck('id');

        foreach ($roleIds as $roleId) {
            foreach ($permissionIds as $permissionId) {
                DB::table('permission_role')->updateOrInsert([
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('permissions')->whereIn('slug', [
            'clinical_assessments.view',
            'clinical_assessments.manage',
        ])->delete();
    }
};
