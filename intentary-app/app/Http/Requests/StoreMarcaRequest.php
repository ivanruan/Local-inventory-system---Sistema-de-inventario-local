<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class StoreMarcaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // O usa gates/policies según tu sistema de permisos
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100', 'unique:marcas,nombre'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la marca es obligatorio',
            'nombre.max' => 'El nombre no debe exceder los 100 caracteres',
            'nombre.unique' => 'Esta marca ya existe en el sistema'
        ];
    }
}
