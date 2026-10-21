<?php

declare(strict_types=1);

namespace App\Rules;

use App\Contracts\RosterStore;
use App\Data\Shifts;
use App\Data\Staff;
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
        if (!in_array($this->day, Shifts::DAYS, true) || !in_array($this->shift, Shifts::names(), true)) {
            return;
        }

        $employee = Staff::find((int) $value);

        if ($employee === null) {
            return;
        }

        // Minutos desde el lunes a las 00:00. El final puede pasar de medianoche.
        $start = Shifts::dayIndex($this->day) * 1440 + Shifts::startsAt($this->shift);
        $end = Shifts::dayIndex($this->day) * 1440 + Shifts::endsAt($this->shift);

        foreach ($this->store->forEmployee($employee->id) as $assignment) {
            $day = $assignment['day'];
            $shift = $assignment['shift'];

            if ($day === $this->day && $shift === $this->shift) {
                continue; // Se está sustituyendo esta celda, no añadiendo otro turno.
            }

            $otherStart = Shifts::dayIndex($day) * 1440 + Shifts::startsAt($shift);
            $otherEnd = Shifts::dayIndex($day) * 1440 + Shifts::endsAt($shift);

            // Se comprueban ambos sentidos, aunque el usuario rellene los días desordenados.
            $rest = $start <= $otherStart ? $otherStart - $end : $start - $otherEnd;

            if ($rest < self::MINIMUM_MINUTES) {
                $hours = max(0, $rest) / 60;
                $fail(
                    "{$employee->name} necesita al menos 12 h de descanso: entre "
                    . "{$this->day} ({$this->shift}) y {$day} ({$shift}) solo habría {$hours} h."
                );

                return;
            }
        }
    }
}
