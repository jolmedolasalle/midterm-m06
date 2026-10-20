<?php

declare(strict_types=1);

namespace App\Rules;

use App\Contracts\RosterStore;
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
        // TODO F04
    }
}
