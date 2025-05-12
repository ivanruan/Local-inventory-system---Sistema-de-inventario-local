<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Producto
 *
 * @property int      $id
 * @property string   $codigo
 * @property string   $nombre
 * @property string   $especificacion
 * @property int      $marca_id
 * @property int      $categoria_id
 * @property int      $ubicacion_id
 * @property string   $unidad
 * @property int      $nivel
 * @property int      $stock_minimo
 * @property int      $stock_actual
 * @property int      $stock_maximo
 * @property int      $stock_seguridad
 * @property int      $duracion_inventario
 * @property string   $status
 * @property float    $costo
 * @property int      $vida_util
 * @property string   $observaciones
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Producto extends Model
{
    use HasFactory;

    // Si fuera necesario cambiar el nombre de la tabla:
    // protected $table = 'productos';

    /**
     * Campos que se pueden asignar masivamente.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'codigo',
        'nombre',
        'especificacion',
        'marca_id',
        'categoria_id',
        'ubicacion_id',
        'unidad',
        'nivel',
        'stock_minimo',
        'stock_actual',
        'stock_maximo',
        'stock_seguridad',
        'duracion_inventario',
        'status',
        'costo',
        'vida_util',
        'observaciones',
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'nivel'               => 'integer',
        'stock_minimo'        => 'integer',
        'stock_actual'        => 'integer',
        'stock_maximo'        => 'integer',
        'stock_seguridad'     => 'integer',
        'duracion_inventario' => 'integer',
        'vida_util'           => 'integer',
        'costo'               => 'decimal:2',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
        // Para enums simples mantenemos string:
        'status'              => 'string',
    ];

    /**
     * Relación: un Producto pertenece a una Marca.
     */
    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    /**
     * Relación: un Producto pertenece a una Categoría.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Relación: un Producto está en una Ubicación.
     */
    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class);
    }

    /**
     * Relación: un Producto tiene muchos Movimientos de Inventario.
     */
    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    /**
     * Relación: un Producto puede generar muchas Alertas de Stock.
     */
    public function alertasStock()
    {
        return $this->hasMany(AlertaStock::class);
    }

    /**
     * Relación: un Producto puede tener muchos Mantenimientos.
     */
    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class);
    }
}

