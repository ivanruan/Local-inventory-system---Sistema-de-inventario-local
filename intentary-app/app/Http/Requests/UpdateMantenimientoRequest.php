<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMantenimientoRequest extends FormRequest
{
	public function authorize(): bool
    {
        return true; // Ajusta según sea necesario
    }

    public function rules(): array
    {
        return [
            'fecha_programada' => 'sometimes|date|after_or_equal:today',
            'fecha_ejecucion' => 'nullable|date|after_or_equal:fecha_programada',
            'tipo' => 'sometimes|in:preventivo,correctivo,limpieza',
            'descripcion' => 'nullable|string',
            'responsable' => 'nullable|string|max:100',
            'status' => 'sometimes|in:pendiente,completado,cancelado',
            'costo' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string',
        ];
    }
}
