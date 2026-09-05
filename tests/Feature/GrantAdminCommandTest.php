<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GrantAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuthorizationSeeder::class);
    }

    public function test_command_assigns_administrator_role_to_existing_user(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@urpe.test',
        ]);

        $this->artisan('urpe:grant-admin', ['email' => $user->email])
            ->expectsOutput('Rol administrator asignado a admin@urpe.test.')
            ->assertExitCode(0);

        $this->assertTrue($user->fresh()->hasRole('administrator'));
    }

    public function test_command_fails_when_user_does_not_exist(): void
    {
        $this->artisan('urpe:grant-admin', ['email' => 'no-existe@urpe.test'])
            ->expectsOutput('No existe un usuario con el correo no-existe@urpe.test.')
            ->assertExitCode(1);
    }
}
