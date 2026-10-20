<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/** F02 · Ficha, excepción y 404. PRUEBA VISIBLE. */
final class F02_EmployeeSheetTest extends TestCase
{
    public function test_la_ficha_de_un_empleado_existente_se_ve(): void
    {
        $this->get(route('employees.show', 1))
            ->assertOk()
            ->assertSee('Marta Ferrer');
    }

    public function test_un_id_que_no_existe_da_404_y_no_una_traza(): void
    {
        $response = $this->get(route('employees.show', 999));

        $response->assertNotFound();
        $response->assertDontSee('EmployeeNotFoundException');  // nunca la traza
        $response->assertDontSee('Stack trace');
    }

    public function test_un_id_que_no_es_numerico_no_llega_al_controlador(): void
    {
        $this->get('/empleados/abc')->assertNotFound();
    }
}
