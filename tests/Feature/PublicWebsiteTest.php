<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicWebsiteTest extends TestCase
{
    public function test_public_home_renders_approved_institutional_content(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Unidad de Rehabilitación Pediátrica Evolutiva')
            ->assertSee('Acompañamos cada paso de su')
            ->assertSee('desarrollo.')
            ->assertSee('A quiénes acompañamos')
            ->assertSee('Método Vojta')
            ->assertSee('Así acompañamos a')
            ->assertSee('Nuestro equipo')
            ->assertSee('Preguntas frecuentes')
            ->assertSee('961 650 8708');
    }

    public function test_public_home_does_not_expose_clinical_system_access(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertDontSee('href="/login"', false)
            ->assertDontSee("href='/login'", false)
            ->assertDontSee('Gestión Clínica');
    }

    public function test_public_home_contains_primary_navigation_anchors(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('id="inicio"', false)
            ->assertSee('id="nosotros"', false)
            ->assertSee('id="terapias"', false)
            ->assertSee('id="equipo"', false)
            ->assertSee('id="contacto"', false);
    }

    public function test_public_home_contains_whatsapp_contact_action(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('wa.me/529616508708', false);
    }
}
