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
            'codigo' => 'sometimes|string|max:50|unique:ubicaciones,codigo,' . $this->getUbicacionId(),
            'nivel' => 'sometimes|integer',
        ];
    }

    private function getUbicacionId()
    {
        $ubicacion = $this->route('ubicacion');

        // Puede ser un modelo o solo el ID (int)
        return is_object($ubicacion) ? $ubicacion->id : $ubicacion;
    }
}

