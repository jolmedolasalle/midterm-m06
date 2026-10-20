<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * F01 · Listado de empleados. PRUEBA VISIBLE: está aquí para guiarte.
 * Si pasa, F01 hace lo que se le pide.
 */
final class F01_EmployeeListTest extends TestCase
{
    public function test_el_listado_muestra_toda_la_plantilla(): void
    {
        $response = $this->get(route('employees.index'));

        $response->assertOk();
        $response->assertSee('Marta Ferrer');
        $response->assertSee('Dani Quintana');
    }

    public function test_el_filtro_por_rol_deja_fuera_a_los_demas(): void
    {
        $response = $this->get(route('employees.index', ['role' => 'almacen']));

        $response->assertOk();
        $response->assertSee('Pau Esteve');          // almacén
        $response->assertDontSee('Marta Ferrer');    // responsable
    }

    public function test_se_puede_ordenar_por_horas_de_contrato(): void
    {
        $response = $this->get(route('employees.index', ['sort' => 'hours']));

        $response->assertOk();
        // El de 20 h tiene que aparecer antes que el de 40 h.
        $body = $response->getContent();
        self::assertLessThan(
            strpos($body, 'Marta Ferrer'),      // 40 h
            strpos($body, 'Nerea Company'),     // 20 h
            'Ordenando por horas, los contratos más cortos van primero.',
        );
    }
}
