<?php

declare(strict_types=1);

namespace App\Rules;

use App\Contracts\RosterStore;
use App\Data\Shifts;
use App\Data\Staff;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** R01 · Un empleado no puede tener dos turnos el mismo día. */
final class NoOverlap implements ValidationRule
{
    /**
     * Ya viene hecho: AssignShiftRequest te pasa el cuadrante actual y el día y
     * el turno que se quieren asignar. En validate() recibes el id del empleado.
     */
    public function __construct(
        private readonly RosterStore $store,
        private readonly string $day,
        private readonly string $shift,
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!in_array($this->day, Shifts::DAYS, true) || !in_array($this->shift, Shifts::names(), true)) {
            return; // Los campos inválidos ya tienen su propio error en el Form Request.
        }

        $employee = Staff::find((int) $value);

        if ($employee === null) {
            return;
        }

        foreach ($this->store->all()[$this->day] as $shift => $employeeId) {
            // Reasignar la misma celda no crea un segundo turno.
            if ($shift !== $this->shift && $employeeId === $employee->id) {
                $fail("{$employee->name} ya tiene el turno de {$shift} el {$this->day}.");

                return;
            }
        }
    }
}
