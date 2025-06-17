<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Producto; // Make sure to import your Producto model

class UpdateProductoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Set this to true to allow authenticated users to make this request
        // You might add more specific authorization logic here if needed (e.g., isAdmin()).
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the ID of the product being updated from the route
        // This assumes your route is something like /productos/{producto}
        $productId = $this->route('producto')->id;

        return [
            'codigo' => [
                'required',
                'string',
                'max:50',
                // Ensure code is unique, but ignore the current product's ID
                Rule::unique('productos')->ignore($productId),
            ],
            'nombre' => 'required|string|max:255',
            'especificacion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'marca_id' => 'nullable|exists:marcas,id',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'unidad' => 'required|string|max:10', // Adjust max length if needed
            'nivel' => 'nullable|integer|min:0|max:99',
            'stock_inicial' => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'stock_maximo' => 'nullable|integer|min:0',
            'stock_seguridad' => 'nullable|integer|min:0',
            'duracion_inventario' => 'nullable|integer|min:1',
            'vida_util' => 'nullable|integer|min:1',
            'status' => ['required', 'string', Rule::in(Producto::getStatusOptions())],
            'costo' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'codigo.required' => 'El código del producto es obligatorio.',
            'codigo.unique' => 'El código del producto ya existe. Por favor, elige uno diferente.',
            'codigo.max' => 'El código no debe exceder los :max caracteres.',
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los :max caracteres.',
            'categoria_id.required' => 'La categoría es obligatoria.',
            'categoria_id.exists' => 'La categoría seleccionada no es válida.',
            'ubicacion_id.required' => 'La ubicación es obligatoria.',
            'ubicacion_id.exists' => 'La ubicación seleccionada no es válida.',
            'unidad.required' => 'La unidad de medida es obligatoria.',
            'stock_inicial.required' => 'El stock inicial es obligatorio.',
            'stock_inicial.min' => 'El stock inicial no puede ser negativo.',
            'costo.required' => 'El costo unitario es obligatorio.',
            'costo.numeric' => 'El costo debe ser un valor numérico.',
            'costo.min' => 'El costo no puede ser negativo.',
            'status.required' => 'El estado del producto es obligatorio.',
            'status.in' => 'El estado seleccionado no es válido.',
            // Add more custom messages as needed for other fields
            'stock_minimo.min' => 'El stock mínimo no puede ser negativo.',
            'stock_maximo.min' => 'El stock máximo no puede ser negativo.',
            'stock_seguridad.min' => 'El stock de seguridad no puede ser negativo.',
            'duracion_inventario.min' => 'La duración del inventario debe ser al menos 1 día.',
            'vida_util.min' => 'La vida útil debe ser al menos 1 día.',
        ];
    }
}
