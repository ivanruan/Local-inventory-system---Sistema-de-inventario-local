<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAutorizacionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'movimiento_id' => 'required|exists:movimientos_inventario,id',
            'autorizador_nombre' => 'nullable|string|max:100',
            'autorizador_cargo' => 'nullable|string|max:100',
            'firma_url' => 'nullable|url|max:200',
            'fecha_autorizacion' => 'required|date',
            'observaciones' => 'nullable|string',
        ];
    }
}

