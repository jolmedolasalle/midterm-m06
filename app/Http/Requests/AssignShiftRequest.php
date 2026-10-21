<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Contracts\RosterStore;
use App\Data\Shifts;
use App\Data\Staff;
use App\Rules\MinimumRest;
use App\Rules\NoOverlap;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * Laravel inyecta el contrato. Cada regla recibe el cuadrante actual y
     * la celda solicitada. bail detiene las reglas de un campo tras el primer
     * error: un id inexistente no llega a las reglas de negocio.
     *
     * @return array<string, mixed>
     */
    public function rules(RosterStore $store): array
    {
        $day = $this->input('day');
        $shift = $this->input('shift');
        $day = is_string($day) ? $day : '';
        $shift = is_string($shift) ? $shift : '';

        return [
            'day' => ['bail', 'required', 'string', Rule::in(Shifts::DAYS)],
            'shift' => ['bail', 'required', 'string', Rule::in(Shifts::names())],
            'employee_id' => [
                'bail',
                'required',
                'integer',
                Rule::in(array_column(Staff::all(), 'id')),
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
            'day.required' => 'Selecciona un día.',
            'day.string' => 'El día debe ser uno de los siete días de la semana.',
            'day.in' => 'El día seleccionado no existe.',
            'shift.required' => 'Selecciona un turno.',
            'shift.string' => 'Selecciona un único turno válido.',
            'shift.in' => 'El turno seleccionado no existe.',
            'employee_id.required' => 'Selecciona un empleado.',
            'employee_id.integer' => 'El identificador del empleado debe ser un número entero.',
            'employee_id.in' => 'El empleado seleccionado no pertenece a la plantilla.',
        ];
    }
}
