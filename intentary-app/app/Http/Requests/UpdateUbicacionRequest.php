<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUbicacionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'codigo' => 'sometimes|string|max:50|unique:ubicaciones,codigo,' . $this->ubicacion->id,
            'nivel' => 'sometimes|integer',
        ];
    }
}
