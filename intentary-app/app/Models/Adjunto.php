<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adjunto extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'relacionado_id',
        'nombre_original',
        'nombre_guardado',
        'extension',
        'tamanio_kb',
        'url',
    ];

    /**
     * Relación polimórfica para adjuntos
     */
    public function relacionado()
    {
        return $this->morphTo(__FUNCTION__, 'tipo', 'relacionado_id');
    }
}

