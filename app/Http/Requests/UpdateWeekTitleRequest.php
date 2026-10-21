<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateWeekTitleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return ['title' => ['nullable', 'string', 'max:160']];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'title.string' => 'El título de la semana debe ser texto.',
            'title.max' => 'El título puede tener como máximo 160 caracteres.',
        ];
    }
}
