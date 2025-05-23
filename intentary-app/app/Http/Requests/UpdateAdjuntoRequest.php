<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdjuntoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo'         => ['nullable', 'string', 'max:50'],
            'ruta_archivo' => ['sometimes', 'string', 'max:255'],
            'descripcion'  => ['nullable', 'string'],
        ];
    }
}

