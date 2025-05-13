<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Adjunto extends Model
{
    use HasFactory;

    protected $table = 'adjuntos';

    protected $fillable = [
        'movimiento_id',
        'tipo',
        'ruta_archivo',
        'descripcion',
    ];

    public $timestamps = true;

    public function movimiento()
    {
        return $this->belongsTo(MovimientoInventario::class, 'movimiento_id');
    }
}

