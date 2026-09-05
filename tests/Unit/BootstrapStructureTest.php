<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class BootstrapStructureTest extends TestCase
{
    public function test_required_public_entry_files_exist(): void
    {
        $projectRoot = dirname(__DIR__, 2);

        $this->assertFileExists($projectRoot.'/public/index.php');
        $this->assertFileExists($projectRoot.'/public/.htaccess');
    }

    public function test_required_laravel_configuration_files_exist(): void
    {
        $projectRoot = dirname(__DIR__, 2);

        foreach ([
            'app.php',
            'auth.php',
            'cache.php',
            'database.php',
            'filesystems.php',
            'logging.php',
            'mail.php',
            'queue.php',
            'services.php',
            'session.php',
        ] as $configFile) {
            $this->assertFileExists($projectRoot.'/config/'.$configFile);
        }
    }

    public function test_required_runtime_structure_exists(): void
    {
        $projectRoot = dirname(__DIR__, 2);

        $this->assertFileExists($projectRoot.'/app/Http/Controllers/Controller.php');
        $this->assertDirectoryExists($projectRoot.'/bootstrap/cache');
        $this->assertDirectoryExists($projectRoot.'/storage/app/private');
        $this->assertDirectoryExists($projectRoot.'/storage/app/public');
        $this->assertDirectoryExists($projectRoot.'/storage/framework/cache/data');
        $this->assertDirectoryExists($projectRoot.'/storage/framework/sessions');
        $this->assertDirectoryExists($projectRoot.'/storage/framework/testing');
        $this->assertDirectoryExists($projectRoot.'/storage/framework/views');
        $this->assertDirectoryExists($projectRoot.'/storage/logs');
    }

    public function test_environment_example_uses_mysql_without_real_credentials(): void
    {
        $projectRoot = dirname(__DIR__, 2);
        $environment = file_get_contents($projectRoot.'/.env.example');

        $this->assertIsString($environment);
        $this->assertStringContainsString('DB_CONNECTION=mysql', $environment);
        $this->assertStringContainsString('DB_DATABASE=urpe', $environment);
        $this->assertStringContainsString('DB_USERNAME=root', $environment);
        $this->assertStringContainsString("DB_PASSWORD=\n", str_replace("\r\n", "\n", $environment));
    }

    public function test_application_configuration_uses_urpe_defaults(): void
    {
        $projectRoot = dirname(__DIR__, 2);
        $applicationConfig = file_get_contents($projectRoot.'/config/app.php');
        $databaseConfig = file_get_contents($projectRoot.'/config/database.php');

        $this->assertIsString($applicationConfig);
        $this->assertIsString($databaseConfig);
        $this->assertStringContainsString("env('APP_TIMEZONE', 'America/Mexico_City')", $applicationConfig);
        $this->assertStringContainsString("env('DB_CONNECTION', 'mysql')", $databaseConfig);
    }
}
