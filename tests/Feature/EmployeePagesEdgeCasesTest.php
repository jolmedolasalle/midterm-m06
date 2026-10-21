<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class EmployeePagesEdgeCasesTest extends TestCase
{
    public function test_los_nombres_con_tildes_se_ordenan_con_el_resto(): void
    {
        $this->get(route('employees.index'))
            ->assertOk()
            ->assertSeeInOrder(['Hugo Marín', 'Iván Sotelo', 'Jordi Vidal'])
            ->assertSeeInOrder(['Nerea Company', 'Óscar Ramírez', 'Pau Esteve']);
    }

    public function test_un_filtro_mal_formado_no_rompe_el_listado(): void
    {
        $this->get(route('employees.index', ['role' => ['almacen'], 'sort' => ['hours']]))
            ->assertOk()
            ->assertSee('Marta Ferrer')
            ->assertSee('Pau Esteve');
    }

    public function test_un_identificador_enorme_muestra_la_pagina_de_error_propia(): void
    {
        $this->get(route('employees.show', ['id' => str_repeat('9', 40)]))
            ->assertNotFound()
            ->assertSee('No hemos encontrado lo que buscas')
            ->assertDontSee('EmployeeNotFoundException')
            ->assertDontSee('Stack trace');
    }
}
