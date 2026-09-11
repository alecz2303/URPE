<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

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

Artisan::command('urpe:preflight {--skip-db : Omitir la prueba de conexión a la base de datos}', function (): int {
    $checks = [];

    $check = function (string $label, bool $passes, string $detail) use (&$checks): void {
        $checks[] = [$passes ? 'OK' : 'FAIL', $label, $detail];
    };

    $check('PHP', PHP_VERSION_ID >= 80400, PHP_VERSION.' (mínimo 8.4)');
    $check('Entorno', config('app.env') === 'production', 'APP_ENV='.config('app.env'));
    $check('Debug', config('app.debug') === false, 'APP_DEBUG='.(config('app.debug') ? 'true' : 'false'));

    $appUrl = (string) config('app.url');
    $check('HTTPS', str_starts_with(strtolower($appUrl), 'https://'), 'APP_URL='.$appUrl);
    $check('APP_KEY', filled(config('app.key')), filled(config('app.key')) ? 'configurada' : 'vacía');
    $check('Base de datos', config('database.default') === 'mysql', 'DB_CONNECTION='.config('database.default'));
    $check('Sesiones persistentes', config('session.driver') === 'database', 'SESSION_DRIVER='.config('session.driver'));
    $check('Cookie segura', config('session.secure') === true, 'SESSION_SECURE_COOKIE='.(config('session.secure') ? 'true' : 'false'));
    $check('Cookie HttpOnly', config('session.http_only') === true, 'SESSION_HTTP_ONLY='.(config('session.http_only') ? 'true' : 'false'));

    $sameSite = strtolower((string) config('session.same_site'));
    $check('SameSite', in_array($sameSite, ['lax', 'strict'], true), 'SESSION_SAME_SITE='.$sameSite);

    $clinicalRoot = rtrim((string) config('filesystems.disks.clinical.root'), DIRECTORY_SEPARATOR);
    $publicRoot = rtrim(public_path(), DIRECTORY_SEPARATOR);
    $expectedClinicalRoot = rtrim(storage_path('app/clinical-private'), DIRECTORY_SEPARATOR);

    $check('Disco clínico', $clinicalRoot === $expectedClinicalRoot, $clinicalRoot ?: 'sin root');
    $check('Privacidad clínica', config('filesystems.disks.clinical.visibility') === 'private' && config('filesystems.disks.clinical.serve') === false, 'visibility='.(string) config('filesystems.disks.clinical.visibility').', serve='.(config('filesystems.disks.clinical.serve') ? 'true' : 'false'));
    $check('Fuera de public', $clinicalRoot !== '' && ! str_starts_with($clinicalRoot.DIRECTORY_SEPARATOR, $publicRoot.DIRECTORY_SEPARATOR), $clinicalRoot ?: 'sin root');
    $check('storage escribible', is_dir(storage_path()) && is_writable(storage_path()), storage_path());
    $check('bootstrap/cache escribible', is_dir(base_path('bootstrap/cache')) && is_writable(base_path('bootstrap/cache')), base_path('bootstrap/cache'));

    if (! $this->option('skip-db')) {
        try {
            DB::connection()->getPdo();
            $check('Conexión DB', true, 'conexión disponible');
        } catch (Throwable $exception) {
            $check('Conexión DB', false, $exception->getMessage());
        }
    } else {
        $checks[] = ['SKIP', 'Conexión DB', 'omitida por --skip-db'];
    }

    $this->table(['Estado', 'Verificación', 'Detalle'], $checks);

    $failures = collect($checks)->where(0, 'FAIL')->count();

    if ($failures > 0) {
        $this->error("Preflight rechazado: {$failures} verificación(es) requieren corrección.");

        return 1;
    }

    $this->info('Preflight aprobado: configuración base de producción lista para smoke test y despliegue.');

    return 0;
})->purpose('Validate the URPE production deployment baseline before release');
