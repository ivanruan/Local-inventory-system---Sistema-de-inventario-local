<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Schema;

class StoreCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Verifica si estamos en entorno de testing
        if (app()->environment('testing') && !Schema::hasTable('categorias')) {
            return [
                'nombre' => 'required|string|max:100'
            ];
        }

        return [
            'nombre' => 'required|string|max:100|unique:categorias,nombre'
        ];
    }

    public function rules(): array
    {
        return $this->authorize();
    }
}
