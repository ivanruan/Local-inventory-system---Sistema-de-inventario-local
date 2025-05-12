<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mantenimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id',
        'fecha_programada',
        'fecha_ejecucion',
        'tipo',
        'descripcion',
        'responsable',
        'status',
        'costo',
        'observaciones',
    ];

    protected $casts = [
        'fecha_programada' => 'datetime',
        'fecha_ejecucion' => 'datetime',
        'costo' => 'decimal:2',
    ];

    // Relación con Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    // Scope para mantenimientos pendientes
    public function scopePendientes($query)
    {
        return $query->where('status', 'pendiente');
    }

    // Scope para mantenimientos completados
    public function scopeCompletados($query)
    {
        return $query->where('status', 'completado');
    }

    // Scope para próximos mantenimientos
    public function scopeProximos($query)
    {
        return $query->where('status', 'pendiente')
                     ->whereDate('fecha_programada', '>=', now())
                     ->orderBy('fecha_programada', 'asc');
    }
}

