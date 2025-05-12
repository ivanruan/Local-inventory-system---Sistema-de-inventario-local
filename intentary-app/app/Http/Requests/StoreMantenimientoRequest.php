<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMantenimientoRequest extends FormRequest
{
   public function authorize(): bool
    {
        return true; // Ajusta según roles/permisos
    }

    public function rules(): array
    {
        return [
            'producto_id' => 'required|exists:productos,id',
            'fecha_programada' => 'required|date|after_or_equal:today',
            'fecha_ejecucion' => 'nullable|date|after_or_equal:fecha_programada',
            'tipo' => 'required|in:preventivo,correctivo,limpieza',
            'descripcion' => 'nullable|string',
            'responsable' => 'nullable|string|max:100',
            'status' => 'required|in:pendiente,completado,cancelado',
            'costo' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string',
        ];
    }
}
