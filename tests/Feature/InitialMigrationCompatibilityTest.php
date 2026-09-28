<?php

namespace Tests\Feature;

use Tests\TestCase;

class InitialMigrationCompatibilityTest extends TestCase
{
    public function test_application_sets_mariadb_safe_default_string_length(): void
    {
        $provider = file_get_contents(app_path('Providers/AppServiceProvider.php'));

        $this->assertIsString($provider);
        $this->assertStringContainsString(
            'Schema::defaultStringLength(191);',
            $provider,
        );
    }

    public function test_password_reset_email_primary_key_remains_explicitly_safe(): void
    {
        $migration = file_get_contents(database_path('migrations/0001_01_01_000000_create_users_table.php'));

        $this->assertIsString($migration);
        $this->assertStringContainsString(
            "\$table->string('email', 191)->primary();",
            $migration,
        );
    }
}
