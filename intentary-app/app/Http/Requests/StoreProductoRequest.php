<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // At this point, we know user is authenticated (thanks to middleware)
        $user = auth()->user();
        
        // Allow if user is not an operator, or if they are an active operator
        return $user->rol !== 'operador' || $user->activo;
    }

    public function rules(): array
    {
        return [
            'codigo'             => 'required|string|max:50|unique:productos,codigo',
            'nombre'             => 'required|string|max:100',
            'especificacion'     => 'required|string|max:200',
            'marca_id'           => 'required|exists:marcas,id',
            'categoria_id'       => 'required|exists:categorias,id',
            'ubicacion_id'       => 'required|exists:ubicaciones,id',
            'unidad'             => 'required|string|max:20',
            'nivel'              => 'nullable|integer|min:0',
            'stock_minimo'       => 'required|integer|min:0',
            'stock_actual'       => 'required|integer|min:0',
            'stock_maximo'       => 'required|integer|min:0',
            'stock_seguridad'    => 'required|integer|min:0',
            'duracion_inventario'=> 'required|integer|min:0',
            'status'             => 'required|in:Stock Bajo, Fuera de Stock, Sobre Stock, Stock Optimo',
            'costo'              => 'required|numeric|min:0',
            'vida_util'          => 'required|integer|min:0',
            'observaciones'      => 'nullable|string',
        ];
    }


    public function messages(): array
    {
        return [
            'nombre.required'      => 'El nombre es obligatorio.',
            'nombre.max'           => 'El nombre no puede exceder 100 caracteres.',
            'especificacion.max'   => 'La especificación no puede exceder 200 caracteres.',
            'marca_id.required'    => 'La marca es obligatoria.',
            'marca_id.exists'      => 'La marca seleccionada no existe.',
            'categoria_id.required'=> 'La categoría es obligatoria.',
            'categoria_id.exists'  => 'La categoría seleccionada no existe.',
            'ubicacion_id.required'=> 'La ubicación es obligatoria.',
            'ubicacion_id.exists'  => 'La ubicación seleccionada no existe.',
            'unidad.required'      => 'La unidad es obligatoria.',
            'unidad.max'           => 'La unidad no puede exceder 20 caracteres.',
            'nivel.required'       => 'El nivel es obligatorio.',
            'nivel.integer'        => 'El nivel debe ser un número entero.',
            'nivel.min'            => 'El nivel debe ser mayor o igual a 0.',
            'stock_minimo.required'=> 'El stock mínimo es obligatorio.',
            'stock_minimo.integer' => 'El stock mínimo debe ser un número entero.',
            'stock_minimo.min'     => 'El stock mínimo debe ser mayor o igual a 0.',
            'stock_actual.required'=> 'El stock actual es obligatorio.',
            'stock_actual.integer' => 'El stock actual debe ser un número entero.',
            'stock_actual.min'     => 'El stock actual debe ser mayor o igual a 0.',
            'stock_maximo.required'=> 'El stock máximo es obligatorio.',
            'stock_maximo.integer' => 'El stock máximo debe ser un número entero.',
            'stock_maximo.min'     => 'El stock máximo debe ser mayor o igual a 0.',
            'stock_seguridad.required'=> 'El stock de seguridad es obligatorio.',
            'stock_seguridad.integer' => 'El stock de seguridad debe ser un número entero.',
            'stock_seguridad.min'     => 'El stock de seguridad debe ser mayor o igual a 0.',
            'duracion_inventario.required'=> 'La duración del inventario es obligatoria.',
            'duracion_inventario.integer' => 'La duración del inventario debe ser un número entero.',
            'duracion_inventario.min'     => 'La duración del inventario debe ser mayor o igual a 0.',
            'status.required'      => 'El estado es obligatorio.',
            'status.in'            => 'El estado debe ser Activo, Inactivo u Obsoleto.',
            'costo.required'       => 'El costo es obligatorio.',
            'costo.numeric'        => 'El costo debe ser un número.',
            'costo.min'            => 'El costo debe ser mayor o igual a 0.',
            'vida_util.required'   => 'La vida útil es obligatoria.',
            'vida_util.integer'    => 'La vida útil debe ser un número entero.',
            'vida_util.min'        => 'La vida útil debe ser mayor o igual a 0.',
        ];
    }
}

