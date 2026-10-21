<?php

declare(strict_types=1);

namespace App\Stores;

use App\Contracts\RosterStore;
use App\Data\Shifts;

/**
 * Guarda el cuadrante en la sesión de Laravel, sin utilizar una base de datos.
 *
 * La clave `roster.week` contiene el mapa día -> turno -> id de empleado o null.
 * La clave `roster.title` contiene el título libre de la semana.
 * Sin datos previos, all() devuelve los siete días y todos los turnos de Shifts
 * vacíos; title() devuelve una cadena vacía y forEmployee() una lista vacía.
 *
 * Los cambios persisten entre peticiones de la misma sesión. Esta clase solo
 * almacena datos: el Form Request comprueba las reglas antes de llamar a assign().
 */
final class SessionRosterStore implements RosterStore
{
    private const WEEK_KEY = 'roster.week';
    private const TITLE_KEY = 'roster.title';

    /** @return array<string, array<string, int|null>> Semana completa, con null en las celdas vacías. */
    public function all(): array
    {
        return session()->get(self::WEEK_KEY, $this->emptyWeek());
    }

    /** Guarda la asignación, sustituyendo la anterior en esa celda, sin validar reglas. */
    public function assign(string $day, string $shift, int $employeeId): void
    {
        $week = $this->all();
        $week[$day][$shift] = $employeeId;
        session()->put(self::WEEK_KEY, $week);
    }

    /** Vacía la celda; quitar una asignación inexistente conserva la semana vacía. */
    public function remove(string $day, string $shift): void
    {
        $week = $this->all();
        $week[$day][$shift] = null;
        session()->put(self::WEEK_KEY, $week);
    }

    /** @return list<array{day: string, shift: string}> Asignaciones en orden de día y turno. */
    public function forEmployee(int $employeeId): array
    {
        $assignments = [];

        foreach ($this->all() as $day => $dayShifts) {
            foreach ($dayShifts as $shift => $id) {
                if ($id === $employeeId) {
                    $assignments[] = ['day' => $day, 'shift' => $shift];
                }
            }
        }

        return $assignments;
    }

    /** Devuelve el título almacenado, o una cadena vacía cuando todavía no hay título. */
    public function title(): string
    {
        return session()->get(self::TITLE_KEY, '');
    }

    /** Guarda el texto original; las vistas son responsables de escapar su salida. */
    public function setTitle(string $title): void
    {
        session()->put(self::TITLE_KEY, $title);
    }

    /** @return array<string, array<string, int|null>> */
    private function emptyWeek(): array
    {
        $week = [];

        foreach (Shifts::DAYS as $day) {
            foreach (Shifts::names() as $shift) {
                $week[$day][$shift] = null;
            }
        }

        return $week;
    }
}
