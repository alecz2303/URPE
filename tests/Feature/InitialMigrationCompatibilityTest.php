<?php

namespace Tests\Feature;

use Tests\TestCase;

class InitialMigrationCompatibilityTest extends TestCase
{
    public function test_string_primary_keys_use_mariadb_safe_lengths(): void
    {
        $migration = file_get_contents(database_path('migrations/0001_01_01_000000_create_users_table.php'));

        $this->assertIsString($migration);

        foreach (['email', 'id'] as $column) {
            $this->assertStringContainsString(
                "\$table->string('{$column}', 191)->primary();",
                $migration,
            );
            $this->assertStringNotContainsString(
                "\$table->string('{$column}')->primary();",
                $migration,
            );
        }
    }
}
