<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuthorizationSeeder::class);
    }

    public function test_web_responses_include_security_headers(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'no-referrer')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    public function test_https_web_responses_include_hsts(): void
    {
        $this->get('https://localhost/login')
            ->assertOk()
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    public function test_patient_listing_is_paginated_and_keeps_total_count(): void
    {
        $actor = $this->userWithPermissions(['patients.view']);

        foreach (range(1, 25) as $number) {
            Patient::query()->create([
                'first_name' => 'Paciente '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'last_name' => 'A',
                'date_of_birth' => '2020-01-01',
                'is_active' => true,
            ]);
        }

        Patient::query()->create([
            'first_name' => 'Paciente final',
            'last_name' => 'ZZZ',
            'date_of_birth' => '2020-01-01',
            'is_active' => true,
        ]);

        $firstPage = $this->actingAs($actor)->get(route('patients.index'));
        $firstPage->assertOk()
            ->assertSee('26 pacientes')
            ->assertSee('Paciente 25')
            ->assertDontSee('Paciente final');

        $this->actingAs($actor)->get(route('patients.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('Paciente final');
    }

    private function userWithPermissions(array $permissions): User
    {
        $role = Role::query()->create([
            'name' => 'Rol readiness de prueba',
            'slug' => 'readiness-test-'.substr(sha1(uniqid('', true)), 0, 12),
            'is_system' => false,
        ]);

        $role->permissions()->sync(
            Permission::query()->whereIn('slug', $permissions)->pluck('id')->all(),
        );

        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }
}
