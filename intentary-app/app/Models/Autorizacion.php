<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autorizacion extends Model
{
    use HasFactory;

    protected $table = 'autorizaciones';

    protected $fillable = [
        'movimiento_id',
        'autorizador_nombre',
        'autorizador_cargo',
        'firma_url',
        'fecha_autorizacion',
        'observaciones',
    ];

    protected $casts = [
        'fecha_autorizacion' => 'datetime',
    ];

    public function movimiento()
    {
        return $this->belongsTo(MovimientoInventario::class, 'movimiento_id');
    }
}

