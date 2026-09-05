<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;

Artisan::command('urpe:status', function (): void {
    $this->info('URPE Gestión Clínica — foundation ready.');
})->purpose('Show the URPE foundation status');

Artisan::command('urpe:grant-admin {email}', function (string $email): int {
    $user = User::query()->where('email', $email)->first();

    if (! $user) {
        $this->error("No existe un usuario con el correo {$email}.");

        return 1;
    }

    $user->assignRole('administrator');

    $this->info("Rol administrator asignado a {$email}.");

    return 0;
})->purpose('Assign the administrator role to an existing URPE user');
