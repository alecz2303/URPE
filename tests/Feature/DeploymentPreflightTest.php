<?php

namespace Tests\Feature;

use Tests\TestCase;

class DeploymentPreflightTest extends TestCase
{
    public function test_preflight_rejects_insecure_production_baseline(): void
    {
        config([
            'app.env' => 'local',
            'app.debug' => true,
            'app.url' => 'http://urpe.test',
            'app.key' => null,
            'database.default' => 'sqlite',
            'session.driver' => 'file',
            'session.secure' => false,
            'session.http_only' => true,
            'session.same_site' => 'lax',
        ]);

        $this->artisan('urpe:preflight', ['--skip-db' => true])
            ->expectsOutputToContain('Preflight rechazado')
            ->assertExitCode(1);
    }

    public function test_preflight_accepts_secure_production_baseline_without_database_probe(): void
    {
        config([
            'app.env' => 'production',
            'app.debug' => false,
            'app.url' => 'https://urpe.example.test',
            'app.key' => 'base64:'.base64_encode(random_bytes(32)),
            'database.default' => 'mysql',
            'session.driver' => 'database',
            'session.secure' => true,
            'session.http_only' => true,
            'session.same_site' => 'lax',
            'filesystems.disks.clinical.root' => storage_path('app/clinical-private'),
            'filesystems.disks.clinical.visibility' => 'private',
            'filesystems.disks.clinical.serve' => false,
        ]);

        $this->artisan('urpe:preflight', ['--skip-db' => true])
            ->expectsOutputToContain('Preflight aprobado')
            ->assertExitCode(0);
    }
}
