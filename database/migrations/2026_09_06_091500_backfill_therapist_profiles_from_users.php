<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $therapistRoleId = DB::table('roles')->where('slug', 'therapist')->value('id');

        if (! $therapistRoleId) {
            return;
        }

        $users = DB::table('users')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->where('role_user.role_id', $therapistRoleId)
            ->select('users.id', 'users.name', 'users.email', 'users.is_active')
            ->get();

        foreach ($users as $user) {
            DB::table('therapists')->updateOrInsert(
                ['user_id' => $user->id],
                [
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_active' => (bool) $user->is_active,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }
    }

    public function down(): void
    {
        // Backfilled therapist profiles may already contain operational data by the time
        // this migration is rolled back, so they are intentionally preserved.
    }
};
