<?php

declare(strict_types=1);

namespace App\Rules;

use App\Contracts\RosterStore;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * R02 · Descanso mínimo de 12 horas entre el final de un turno y el principio
 * del siguiente.
 *
 * CALCÚLALO a partir de App\Data\Shifts. Si escribes a mano qué combinaciones
 * fallan, funcionará hoy y dejará de funcionar en cuanto cambie una hora de la
 * tabla — y cuatro de las optativas reutilizan este cálculo.
 */
final class MinimumRest implements ValidationRule
{
    public const MINIMUM_MINUTES = 12 * 60;

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
