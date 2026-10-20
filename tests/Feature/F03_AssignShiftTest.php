<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/** F03 · Asignar, quitar, redirigir y avisar. PRUEBA VISIBLE. */
final class F03_AssignShiftTest extends TestCase
{
    public function test_asignar_redirige_con_mensaje_flash(): void
    {
        $response = $this->post(route('roster.assign'), [
            'day' => 'lunes', 'shift' => 'manana', 'employee_id' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
    }

    public function test_el_turno_asignado_se_ve_en_la_rejilla(): void
    {
        $this->post(route('roster.assign'), [
            'day' => 'martes', 'shift' => 'tarde', 'employee_id' => 3,
        ]);

        $this->get(route('roster.index'))->assertOk()->assertSee('Laia Puig');
    }

    public function test_el_mensaje_flash_desaparece_al_recargar(): void
    {
        $this->post(route('roster.assign'), [
            'day' => 'lunes', 'shift' => 'noche', 'employee_id' => 4,
        ])->assertRedirect();

        $this->get(route('roster.index'));                       // lo consume
        $this->get(route('roster.index'))->assertSessionMissing('status');
    }
}
