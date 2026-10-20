<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Contracts\RosterStore;
use App\Rules\MinimumRest;
use App\Rules\NoOverlap;
use Illuminate\Foundation\Http\FormRequest;

/**
 * F04 · Toda la validación de una asignación vive aquí, no en el controlador.
 */
final class AssignShiftRequest extends FormRequest
{
    /**
     * Esta aplicación no tiene usuarios, así que no hay nada que autorizar.
     * Devolver true aquí es correcto EN ESTA PRÁCTICA; en una con login, no.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Además de lo evidente — que el día, el turno y el empleado existan — las
     * dos reglas bloqueantes (R01 y R02) se implementan como reglas propias.
     *
     * Las dos reglas propias ya están enchufadas: Laravel inyecta aquí el
     * RosterStore igual que en un controlador, y cada regla recibe el cuadrante
     * y el día y el turno del formulario. Te falta lo evidente, y el cuerpo de
     * las dos reglas.
     *
     * @return array<string, mixed>
     */
    public function rules(RosterStore $store): array
    {
        $day = (string) $this->input('day');
        $shift = (string) $this->input('shift');

        return [
            // TODO F04: day y shift, que existan en App\Data\Shifts
            'employee_id' => [
                // TODO F04: que el empleado exista en App\Data\Staff
                new NoOverlap($store, $day, $shift),
                new MinimumRest($store, $day, $shift),
            ],
        ];
    }

    /**
     * Cuando se rechaza una asignación el usuario tiene que entender por qué:
     * «Marta ya tiene el turno de tarde el martes» sirve; «asignación no válida» no.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // TODO F04
        ];
    }
}
