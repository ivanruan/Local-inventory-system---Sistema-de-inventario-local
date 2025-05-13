<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdjuntoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ajusta según el control de acceso
    }

    public function rules(): array
    {
        return [
            'movimiento_id' => ['required', 'exists:movimientos_inventario,id'],
            'tipo'          => ['nullable', 'string', 'max:50'],
            'ruta_archivo'  => ['required', 'string', 'max:255'],
            'descripcion'   => ['nullable', 'string'],
        ];
    }
}

