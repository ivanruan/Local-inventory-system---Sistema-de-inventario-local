<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAlertaStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->activo;
    }

    public function rules(): array
    {
        return [
            'producto_id'      => 'required|exists:productos,id',
            'fecha_generacion' => 'nullable|date',
            'tipo_alerta'      => 'required|in:Stock Bajo,Stock Excedido,Vida Útil Límite',
            'nivel_critico'    => 'required|in:Alto,Medio,Bajo',
            'resuelta'         => 'sometimes|boolean',
            'resuelta_en'      => 'nullable|date|required_if:resuelta,true',
        ];
    }

    public function messages(): array
    {
        return [
            'resuelta_en.required_if' => 'La fecha de resolución es obligatoria cuando la alerta está resuelta.',
        ];
    }
}
