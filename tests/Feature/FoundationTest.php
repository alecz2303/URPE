<?php

namespace Tests\Feature;

use Tests\TestCase;

class FoundationTest extends TestCase
{
    public function test_home_page_renders_public_urpe_website_without_clinical_system_links(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Acompañamos cada paso de su')
            ->assertSee('Solicita una valoración')
            ->assertSee('URPE - Unidad de Rehabilitación Pediátrica Evolutiva')
            ->assertDontSee('URPE Gestión Clínica')
            ->assertDontSee('Iniciar sesión')
            ->assertDontSee(route('login'));
    }
}
