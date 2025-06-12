<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMovimientoInventarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo verificar que el usuario esté autenticado
        // El middleware 'auth' ya se encarga de la autenticación en las rutas
        return auth()->check();
        
        // Si necesitas verificar el campo 'activo' y existe en tu tabla usuarios:
        // return auth()->check() && (auth()->user()->activo ?? true);
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
            'usuario_destino'        => 'nullable|exists:usuarios,id', // Cambiado de usuario_id a usuario_destino según tu formulario
            'precio_unitario'        => 'nullable|numeric|min:0',
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
            'fecha_hora.required'    => 'La fecha y hora son obligatorias.',
            'fecha_hora.date'        => 'La fecha y hora deben tener un formato válido.',
            'tipo.required'          => 'El tipo de movimiento es obligatorio.',
            'tipo.in'                => 'El tipo debe ser "entrada" o "salida".',
            'cantidad.required'      => 'La cantidad es obligatoria.',
            'cantidad.numeric'       => 'La cantidad debe ser un número.',
            'cantidad.min'           => 'La cantidad debe ser al menos 0.01.',
            'producto_id.required'   => 'Debe seleccionar un producto.',
            'producto_id.exists'     => 'El producto seleccionado no existe.',
            'proveedor_id.exists'    => 'El proveedor seleccionado no existe.',
            'proyecto_id.exists'     => 'El proyecto seleccionado no existe.',
            'usuario_destino.exists' => 'El usuario destino seleccionado no existe.',
            'precio_unitario.numeric' => 'El precio unitario debe ser un número.',
            'precio_unitario.min'    => 'El precio unitario no puede ser negativo.',
        ];
    }
}
