<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    use HasFactory;

    protected $table = 'ubicaciones';

    protected $fillable = [
        'codigo',
        'nivel',
    ];

    // Relaciones (si luego deseas jerarquía o inventario relacionado, se podrían agregar aquí)
    // Ejemplo futuro:
    // public function productos()
    // {
    //     return $this->hasMany(Producto::class);
    // }
}

