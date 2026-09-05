<?php

namespace Tests\Feature;

use Tests\TestCase;

class FoundationTest extends TestCase
{
    public function test_home_page_identifies_urpe_gestion_clinica(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('URPE Gestión Clínica');
    }
}
