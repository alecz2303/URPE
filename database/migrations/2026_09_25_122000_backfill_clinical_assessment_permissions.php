<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissionIds = DB::table('permissions')
            ->whereIn('slug', ['clinical_assessments.view', 'clinical_assessments.manage'])
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
        $permissionIds = DB::table('permissions')
            ->whereIn('slug', ['clinical_assessments.view', 'clinical_assessments.manage'])
            ->pluck('id');

        $roleIds = DB::table('roles')
            ->whereIn('slug', ['administrator', 'clinical_coordination'])
            ->pluck('id');

        DB::table('permission_role')
            ->whereIn('permission_id', $permissionIds)
            ->whereIn('role_id', $roleIds)
            ->delete();
    }
};
