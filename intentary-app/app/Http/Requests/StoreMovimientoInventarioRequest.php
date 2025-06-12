<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovimientoInventarioRequest extends FormRequest
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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('tipo') === 'entrada') {
                // Puedes hacer proveedor_id y precio_unitario requeridos aquí si es necesario
                // if (!$this->input('proveedor_id')) {
                //     $validator->errors()->add('proveedor_id', 'El proveedor es obligatorio para una entrada.');
                // }
            } elseif ($this->input('tipo') === 'salida') {
                if (!$this->input('proyecto_id')) {
                    $validator->errors()->add('proyecto_id', 'El proyecto es obligatorio para una salida.');
                }
                if (!$this->input('usuario_destino')) {
                    $validator->errors()->add('usuario_destino', 'El usuario destino es obligatorio para una salida.');
                }
                // También puedes agregar validación de stock aquí para la cantidad
                $producto = \App\Models\Producto::find($this->input('producto_id'));
                if ($producto && $this->input('cantidad') > $producto->stock) {
                    $validator->errors()->add('cantidad', 'La cantidad solicitada excede el stock disponible.');
                }
            }
        });
    }
}