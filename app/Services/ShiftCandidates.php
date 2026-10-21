<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\RosterStore;
use App\Data\Employee;
use App\Data\Staff;
use App\Rules\MinimumRest;
use App\Rules\NoOverlap;
use Illuminate\Support\Facades\Validator;

/** Búsqueda compartida por la lista de candidatos y el relleno de un turno. */
final class ShiftCandidates
{
    /**
     * Recibe una celda válida y vacía, comprobada por EmptyShiftRequest.
     * Conserva el orden de Staff::all(): el primero es el primero de la plantilla.
     *
     * @return list<Employee> Personas que respetan R01 y R02, sin modificar el cuadrante.
     */
    public static function forSlot(RosterStore $store, string $day, string $shift): array
    {
        $candidates = [];

        foreach (Staff::all() as $employee) {
            // Reutilizamos las mismas reglas de la asignación manual, sin repetir sus cálculos.
            $validator = Validator::make(
                ['employee_id' => $employee->id],
                ['employee_id' => [
                    'bail',
                    new NoOverlap($store, $day, $shift),
                    new MinimumRest($store, $day, $shift),
                ]]
            );

            if ($validator->passes()) {
                $candidates[] = $employee;
            }
        }

        return $candidates;
    }
}
