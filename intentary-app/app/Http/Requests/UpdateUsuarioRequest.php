<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre' => 'sometimes|string|max:100',
            'email' => [
                'sometimes',
                'email',
                'max:100',
                Rule::unique('usuarios')->ignore($this->usuario),
            ],
            'password' => 'sometimes|string|min:8|confirmed',
            'rol' => 'sometimes|in:admin,operador,supervisor',
            'activo' => 'sometimes|boolean',
        ];
    }
}

