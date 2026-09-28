<?php

namespace Tests\Feature;

use Tests\TestCase;

class InitialMigrationCompatibilityTest extends TestCase
{
    public function test_password_reset_email_primary_key_uses_mariadb_safe_length(): void
    {
        $migration = file_get_contents(database_path('migrations/0001_01_01_000000_create_users_table.php'));

        $this->assertIsString($migration);
        $this->assertStringContainsString(
            "\$table->string('email', 191)->primary();",
            $migration,
        );
        $this->assertStringNotContainsString(
            "\$table->string('email')->primary();",
            $migration,
        );
    }
}
