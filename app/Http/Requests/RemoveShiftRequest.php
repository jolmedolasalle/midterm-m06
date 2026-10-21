<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Data\Shifts;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class RemoveShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'day' => ['bail', 'required', 'string', Rule::in(Shifts::DAYS)],
            'shift' => ['bail', 'required', 'string', Rule::in(Shifts::names())],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'day.required' => 'Indica el día del turno que quieres quitar.',
            'day.string' => 'Selecciona un día válido.',
            'day.in' => 'El día seleccionado no existe.',
            'shift.required' => 'Indica el turno que quieres quitar.',
            'shift.string' => 'Selecciona un turno válido.',
            'shift.in' => 'El turno seleccionado no existe.',
        ];
    }
}
