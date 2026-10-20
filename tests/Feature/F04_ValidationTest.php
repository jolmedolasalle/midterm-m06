<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * F04 · Las dos reglas bloqueantes. PRUEBA VISIBLE.
 *
 * Las tres transiciones ilegales salen de la tabla de turnos, no de una lista:
 * tarde→manana (8 h), noche→manana (0 h) y noche→tarde (8 h).
 */
final class F04_ValidationTest extends TestCase
{
    public function test_r01_un_empleado_no_puede_tener_dos_turnos_el_mismo_dia(): void
    {
        $this->post(route('roster.assign'), ['day' => 'lunes', 'shift' => 'manana', 'employee_id' => 5]);

        $this->post(route('roster.assign'), ['day' => 'lunes', 'shift' => 'noche', 'employee_id' => 5])
            ->assertSessionHasErrors();
    }

    #[DataProvider('transicionesIlegales')]
    public function test_r02_descanso_minimo_de_12_horas(string $shiftDay1, string $shiftDay2): void
    {
        $this->post(route('roster.assign'), ['day' => 'lunes', 'shift' => $shiftDay1, 'employee_id' => 6]);

        $this->post(route('roster.assign'), ['day' => 'martes', 'shift' => $shiftDay2, 'employee_id' => 6])
            ->assertSessionHasErrors();
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function transicionesIlegales(): array
    {
        return [
            'tarde -> manana (8 h)' => ['tarde', 'manana'],
            'noche -> manana (0 h)' => ['noche', 'manana'],
            'noche -> tarde (8 h)'  => ['noche', 'tarde'],
        ];
    }

    public function test_una_transicion_legal_se_acepta(): void
    {
        $this->post(route('roster.assign'), ['day' => 'lunes', 'shift' => 'manana', 'employee_id' => 7]);

        // manana -> manana son 16 h de descanso: es legal.
        $this->post(route('roster.assign'), ['day' => 'martes', 'shift' => 'manana', 'employee_id' => 7])
            ->assertSessionHasNoErrors();
    }
}
