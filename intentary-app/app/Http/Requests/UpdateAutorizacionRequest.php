<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAutorizacionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'movimiento_id' => 'sometimes|exists:movimientos_inventario,id',
            'autorizador_nombre' => 'nullable|string|max:100',
            'autorizador_cargo' => 'nullable|string|max:100',
            'firma_url' => 'nullable|url|max:200',
            'fecha_autorizacion' => 'sometimes|date',
            'observaciones' => 'nullable|string',
        ];
    }
}

