<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovimientoInventarioRequest extends FormRequest
{
	    public function authorize(): bool
    {
        // Solo usuarios activos pueden registrar movimientos
        return auth()->check() && auth()->user()->activo;
    }

    public function rules(): array
    {
        return [
            'fecha_hora'             => 'required|date',
            'tipo'                   => 'required|in:entrada,salida',
            'cantidad'               => 'required|numeric|min:0.01',
            'producto_id'            => 'required|exists:productos,id',
            'proveedor_id'           => 'nullable|exists:proveedores,id',
            'proyecto_id'            => 'nullable|exists:proyectos,id',
            'usuario_id'             => 'required|exists:usuarios,id',
            'tiempo_uso_acumulado'   => 'nullable|numeric|min:0',
            'documento_ingreso'      => 'nullable|string|max:150',
            'motivo_salida'          => 'nullable|string|max:200',
            'procedimiento_disposicion' => 'nullable|string',
            'observaciones'          => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_hora.required'   => 'La fecha y hora son obligatorias.',
            'tipo.in'               => 'El tipo debe ser "entrada" o "salida".',
            'cantidad.min'          => 'La cantidad debe ser al menos 0.01.',
            'producto_id.exists'    => 'El producto seleccionado no existe.',
            'usuario_id.exists'     => 'El usuario seleccionado no existe.',
            // Agrega más mensajes personalizados según necesites
        ];
    }
}
