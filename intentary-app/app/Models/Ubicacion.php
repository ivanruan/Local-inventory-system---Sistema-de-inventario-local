<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ubicacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ubicaciones';

    protected $fillable = [
        'codigo',
        'nivel',
    ];

    protected $dates = ['deleted_at'];

    // Relaciones (si luego deseas jerarquía o inventario relacionado, se podrían agregar aquí)
    // Ejemplo futuro:
    // public function productos()
    // {
    //     return $this->hasMany(Producto::class);
    // }
}

