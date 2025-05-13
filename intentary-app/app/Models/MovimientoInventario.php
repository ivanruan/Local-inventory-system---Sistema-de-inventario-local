<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MovimientoInventario
 *
 * @property int                             $id
 * @property \Illuminate\Support\Carbon     $fecha_hora
 * @property string                          $tipo
 * @property float                           $cantidad
 * @property int                             $producto_id
 * @property int|null                        $proveedor_id
 * @property int|null                        $proyecto_id
 * @property int                             $usuario_id
 * @property float                           $tiempo_uso_acumulado
 * @property string|null                     $documento_ingreso
 * @property string|null                     $motivo_salida
 * @property string|null                     $procedimiento_disposicion
 * @property string|null                     $observaciones
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|MovimientoInventario entrada()
 * @method static \Illuminate\Database\Eloquent\Builder|MovimientoInventario salida()
 */
class MovimientoInventario extends Model
{
    use HasFactory;

    // Nombre de la tabla si fuera distinto
    protected $table = 'movimientos_inventario';

    /**
     * Atributos asignables masivamente.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'fecha_hora',
        'tipo',
        'cantidad',
        'producto_id',
        'proveedor_id',
        'proyecto_id',
        'usuario_id',
        'tiempo_uso_acumulado',
        'documento_ingreso',
        'motivo_salida',
        'procedimiento_disposicion',
        'observaciones',
    ];

    /**
     * Conversion de atributos a tipos nativos.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'fecha_hora'           => 'datetime',
        'cantidad'             => 'decimal:2',
        'tiempo_uso_acumulado' => 'decimal:2',
    ];

    /**
     * Scope para filtrar solo entradas.
     */
    public function scopeEntrada($query)
    {
        return $query->where('tipo', 'entrada');
    }

    /**
     * Scope para filtrar solo salidas.
     */
    public function scopeSalida($query)
    {
        return $query->where('tipo', 'salida');
    }

    /**
     * Relación: un movimiento pertenece a un producto.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Relación: un movimiento puede pertenecer a un proveedor.
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    /**
     * Relación: un movimiento puede pertenecer a un proyecto.
     */
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    /**
     * Relación: un movimiento fue registrado por un usuario.
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Relación: un movimiento puede tener múltiples autorizaciones.
     */
    public function autorizaciones()
    {
        return $this->hasMany(Autorizacion::class);
    }

    /**
     * Relación: un movimiento puede tener múltiples adjuntos.
     */
    public function adjuntos()
    {
        return $this->hasMany(Adjunto::class);
    }
}
