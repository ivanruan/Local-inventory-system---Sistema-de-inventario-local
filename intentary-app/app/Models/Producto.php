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
 * @property int|null $marca_id
 * @property int      $categoria_id
 * @property int      $ubicacion_id
 * @property int|null $proveedor_id
 * @property string   $unidad
 * @property int      $nivel
 * @property int      $stock_inicial
 * @property int      $total_entradas
 * @property int      $total_salidas
 * @property int|null $stock_minimo
 * @property int      $stock_actual
 * @property int|null $stock_maximo
 * @property int|null $stock_seguridad
 * @property int|null $duracion_inventario
 * @property string   $status
 * @property float    $costo
 * @property int|null $vida_util
 * @property string|null $observaciones
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
        'proveedor_id',
        'unidad',
        'nivel',
        'stock_inicial',
        'total_entradas',
        'total_salidas',
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
        'marca_id'            => 'integer',
        'categoria_id'        => 'integer',
        'ubicacion_id'        => 'integer',
        'proveedor_id'        => 'integer',
        'nivel'               => 'integer',
        'stock_inicial'       => 'integer',
        'total_entradas'      => 'integer',
        'total_salidas'       => 'integer',
        'stock_minimo'        => 'integer',
        'stock_actual'        => 'integer',
        'stock_maximo'        => 'integer',
        'stock_seguridad'     => 'integer',
        'duracion_inventario' => 'integer',
        'vida_util'           => 'integer',
        'costo'               => 'decimal:2',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
        'status'              => 'string',
    ];

    /**
     * Valores por defecto para atributos.
     *
     * @var array<string,mixed>
     */
    protected $attributes = [
        'nivel' => 0,
        'stock_inicial' => 0,
        'total_entradas' => 0,
        'total_salidas' => 0,
        'stock_actual' => 0,
        'costo' => 0.00,
        'status' => 'Activo',
    ];

    /**
     * Constantes para los estados del producto.
     */
    public const STATUS_ACTIVO = 'Activo';
    public const STATUS_INACTIVO = 'Inactivo';
    public const STATUS_OBSOLETO = 'Obsoleto';
    public const STATUS_STOCK_OPTIMO = 'Stock Optimo';
    public const STATUS_STOCK_BAJO = 'Stock Bajo';
    public const STATUS_FUERA_STOCK = 'Fuera de Stock';
    public const STATUS_SOBRE_STOCK = 'Sobre Stock';
    public const STATUS_EN_REORDEN = 'En Reorden';

    /**
     * Obtiene todos los estados posibles.
     *
     * @return array<string>
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_ACTIVO,
            self::STATUS_INACTIVO,
            self::STATUS_OBSOLETO,
            self::STATUS_STOCK_OPTIMO,
            self::STATUS_STOCK_BAJO,
            self::STATUS_FUERA_STOCK,
            self::STATUS_SOBRE_STOCK,
            self::STATUS_EN_REORDEN,
        ];
    }

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
     * Relación: un Producto pertenece a un Proveedor.
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
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

    /**
     * Scopes para consultas frecuentes.
     */
    public function scopeActivos($query)
    {
        return $query->where('status', self::STATUS_ACTIVO);
    }

    public function scopeConStockBajo($query)
    {
        return $query->whereColumn('stock_actual', '<=', 'stock_minimo')
                    ->whereNotNull('stock_minimo');
    }

    public function scopeFueraDeStock($query)
    {
        return $query->where('stock_actual', 0);
    }

    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    public function scopePorUbicacion($query, $ubicacionId)
    {
        return $query->where('ubicacion_id', $ubicacionId);
    }

    public function scopePorMarca($query, $marcaId)
    {
        return $query->where('marca_id', $marcaId);
    }

    /**
     * Accessors y Mutators.
     */
    public function getStockCalculadoAttribute(): int
    {
        return $this->stock_inicial + $this->total_entradas - $this->total_salidas;
    }

    public function getEsStockBajoAttribute(): bool
    {
        return $this->stock_minimo !== null && $this->stock_actual <= $this->stock_minimo;
    }

    public function getEsFueraDeStockAttribute(): bool
    {
        return $this->stock_actual === 0;
    }

    public function getEsSobreStockAttribute(): bool
    {
        return $this->stock_maximo !== null && $this->stock_actual > $this->stock_maximo;
    }

    public function getDiasInventarioAttribute(): ?int
    {
        if (!$this->duracion_inventario) {
            return null;
        }

        $fechaIngreso = $this->created_at;
        $diasTranscurridos = now()->diffInDays($fechaIngreso);
        
        return max(0, $this->duracion_inventario - $diasTranscurridos);
    }

    /**
     * Métodos de utilidad.
     */
    public function actualizarStockCalculado(): void
    {
        $stockCalculado = $this->stock_inicial + $this->total_entradas - $this->total_salidas;
        $this->update(['stock_actual' => $stockCalculado]);
    }

    public function determinarEstadoStock(): string
    {
        if ($this->stock_actual === 0) {
            return self::STATUS_FUERA_STOCK;
        }

        if ($this->stock_minimo !== null && $this->stock_actual <= $this->stock_minimo) {
            return self::STATUS_STOCK_BAJO;
        }

        if ($this->stock_maximo !== null && $this->stock_actual > $this->stock_maximo) {
            return self::STATUS_SOBRE_STOCK;
        }

        return self::STATUS_STOCK_OPTIMO;
    }

    /**
     * Boot del modelo.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($producto) {
            // Validar que stock de seguridad no sea mayor al stock máximo
            if ($producto->stock_seguridad !== null && 
                $producto->stock_maximo !== null && 
                $producto->stock_seguridad > $producto->stock_maximo) {
                throw new \Exception('El stock de seguridad no puede ser mayor al stock máximo');
            }

            // Validar que stock mínimo no sea mayor al stock máximo
            if ($producto->stock_minimo !== null && 
                $producto->stock_maximo !== null && 
                $producto->stock_minimo > $producto->stock_maximo) {
                throw new \Exception('El stock mínimo no puede ser mayor al stock máximo');
            }

            // Actualizar estado automáticamente basado en stock
            if (in_array($producto->status, [
                self::STATUS_STOCK_OPTIMO,
                self::STATUS_STOCK_BAJO,
                self::STATUS_FUERA_STOCK,
                self::STATUS_SOBRE_STOCK
            ])) {
                $producto->status = $producto->determinarEstadoStock();
            }
        });

        static::updating(function ($producto) {
            // Recalcular stock actual si cambian los valores base
            if ($producto->isDirty(['stock_inicial', 'total_entradas', 'total_salidas'])) {
                $producto->stock_actual = $producto->stock_inicial + 
                                        $producto->total_entradas - 
                                        $producto->total_salidas;
            }
        });
    }
}