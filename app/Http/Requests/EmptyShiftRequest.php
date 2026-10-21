<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Contracts\RosterStore;
use App\Data\Shifts;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** F08 y F09 solo trabajan con celdas vacías. */
final class EmptyShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(RosterStore $store): array
    {
        $day = $this->input('day');
        $day = is_string($day) ? $day : '';

        return [
            'day' => ['bail', 'required', 'string', Rule::in(Shifts::DAYS)],
            'shift' => [
                'bail',
                'required',
                'string',
                Rule::in(Shifts::names()),
                function (string $attribute, mixed $value, Closure $fail) use ($store, $day): void {
                    if (in_array($day, Shifts::DAYS, true) && $store->all()[$day][$value] !== null) {
                        $fail('Ese turno ya está cubierto. Elige una celda vacía para buscar candidatos.');
                    }
                },
            ],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'day.required' => 'Selecciona el día que quieres cubrir.',
            'day.string' => 'Selecciona un único día válido.',
            'day.in' => 'El día seleccionado no existe.',
            'shift.required' => 'Selecciona el turno que quieres cubrir.',
            'shift.string' => 'Selecciona un único turno válido.',
            'shift.in' => 'El turno seleccionado no existe.',
        ];
    }

    protected function getRedirectUrl(): string
    {
        // Una consulta GET incorrecta vuelve al formulario, no a su propia URL inválida.
        return route('roster.index');
    }
}
