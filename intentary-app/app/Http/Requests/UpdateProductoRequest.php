<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->rol !== 'operador' || auth()->user()->activo;
    }

    public function rules(): array
    {
        $productoId = $this->route('producto')->id;

        return [
            'codigo'             => "required|string|max:50|unique:productos,codigo,{$productoId}",
            'nombre'             => 'required|string|max:100',
            'especificacion'     => 'nullable|string|max:200',
            'marca_id'           => 'required|exists:marcas,id',
            'categoria_id'       => 'required|exists:categorias,id',
            'ubicacion_id'       => 'required|exists:ubicaciones,id',
            'unidad'             => 'required|string|max:20',
            'nivel'              => 'required|integer|min:0',
            'stock_minimo'       => 'required|integer|min:0',
            'stock_actual'       => 'required|integer|min:0',
            'stock_maximo'       => 'required|integer|min:0',
            'stock_seguridad'    => 'required|integer|min:0',
            'duracion_inventario'=> 'required|integer|min:0',
            'status'             => 'required|in:Activo,Inactivo,Obsoleto',
            'costo'              => 'required|numeric|min:0',
            'vida_util'          => 'required|integer|min:0',
            'observaciones'      => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required'      => 'El código es obligatorio.',
            'codigo.unique'        => 'Este código ya está en uso por otro producto.',
            // ... puedes replicar los mensajes de StoreProductoRequest
        ];
    }
}

