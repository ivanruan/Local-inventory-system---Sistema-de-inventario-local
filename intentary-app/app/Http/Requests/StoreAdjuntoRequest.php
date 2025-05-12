<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdjuntoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cambia a lógica de autorización si es necesario
    }

    public function rules(): array
    {
        return [
            'tipo' => 'required|string|max:100',
            'relacionado_id' => 'required|integer',
            'archivo' => 'required|file|max:5120', // Máx 5MB
        ];
    }
}

