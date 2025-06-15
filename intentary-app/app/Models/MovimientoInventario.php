<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\MovimientoInventario
 *
 * @property int                             $id
 * @property string                          $numero_movimiento
 * @property \Illuminate\Support\Carbon     $fecha_hora
 * @property string                          $tipo
 * @property string|null                     $subtipo
 * @property float                           $cantidad
 * @property float|null                      $costo_unitario
 * @property float|null                      $costo_total
 * @property int                             $producto_id
 * @property int|null                        $proveedor_id
 * @property int|null                        $proyecto_id
 * @property int|null                        $ubicacion_id
 * @property int                             $usuario_id
 * @property int|null                        $stock_anterior
 * @property int|null                        $stock_posterior
 * @property string|null                     $numero_documento
 * @property string|null                     $documento_soporte
 * @property string|null                     $motivo
 * @property string|null                     $procedimiento_disposicion
 * @property float|null                      $tiempo_uso_acumulado
 * @property \Illuminate\Support\Carbon|null $fecha_vencimiento
 * @property string|null                     $numero_lote
 * @property string                          $status
 * @property string|null                     $observaciones
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventario';

    /**
     * Atributos asignables masivamente.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'numero_movimiento',
        'fecha_hora',
        'tipo',
        'subtipo',
        'cantidad',
        'costo_unitario',
        'costo_total',
        'producto_id',
        'proveedor_id',
        'proyecto_id',
        'ubicacion_id',
        'usuario_id',
        'stock_anterior',
        'stock_posterior',
        'numero_documento',
        'documento_soporte',
        'motivo',
        'procedimiento_disposicion',
        'tiempo_uso_acumulado',
        'fecha_vencimiento',
        'numero_lote',
        'status',
        'observaciones',
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'fecha_hora'           => 'datetime',
        'cantidad'             => 'decimal:3',
        'costo_unitario'       => 'decimal:2',
        'costo_total'          => 'decimal:2',
        'producto_id'          => 'integer',
        'proveedor_id'         => 'integer',
        'proyecto_id'          => 'integer',
        'ubicacion_id'         => 'integer',
        'usuario_id'           => 'integer',
        'stock_anterior'       => 'integer',
        'stock_posterior'      => 'integer',
        'tiempo_uso_acumulado' => 'decimal:2',
        'fecha_vencimiento'    => 'date',
        'created_at'           => 'datetime',
        'updated_at'           => 'datetime',
    ];

    /**
     * Valores por defecto para atributos.
     *
     * @var array<string,mixed>
     */
    protected $attributes = [
        'status' => 'completado',
    ];

    /**
     * Constantes para tipos de movimiento.
     */
    public const TIPO_ENTRADA = 'entrada';
    public const TIPO_SALIDA = 'salida';
    public const TIPO_AJUSTE = 'ajuste';
    public const TIPO_TRANSFERENCIA = 'transferencia';

    /**
     * Constantes para subtipos de movimiento.
     */
    public const SUBTIPO_COMPRA = 'compra';
    public const SUBTIPO_DEVOLUCION_CLIENTE = 'devolucion_cliente';
    public const SUBTIPO_DEVOLUCION_PROVEEDOR = 'devolucion_proveedor';
    public const SUBTIPO_AJUSTE_INVENTARIO = 'ajuste_inventario';
    public const SUBTIPO_TRANSFERENCIA_ENTRADA = 'transferencia_entrada';
    public const SUBTIPO_VENTA = 'venta';
    public const SUBTIPO_CONSUMO_PROYECTO = 'consumo_proyecto';
    public const SUBTIPO_BAJA_OBSOLESCENCIA = 'baja_obsolescencia';
    public const SUBTIPO_TRANSFERENCIA_SALIDA = 'transferencia_salida';
    public const SUBTIPO_MERMA = 'merma';
    public const SUBTIPO_ROBO_EXTRAVIO = 'robo_extravío';

    /**
     * Constantes para estados del movimiento.
     */
    public const STATUS_PENDIENTE = 'pendiente';
    public const STATUS_COMPLETADO = 'completado';
    public const STATUS_CANCELADO = 'cancelado';

    /**
     * Obtiene todos los tipos de movimiento.
     *
     * @return array<string>
     */
    public static function getTiposMovimiento(): array
    {
        return [
            self::TIPO_ENTRADA,
            self::TIPO_SALIDA,
            self::TIPO_AJUSTE,
            self::TIPO_TRANSFERENCIA,
        ];
    }

    /**
     * Obtiene todos los subtipos por tipo de movimiento.
     *
     * @return array<string,array<string>>
     */
    public static function getSubtiposPorTipo(): array
    {
        return [
            self::TIPO_ENTRADA => [
                self::SUBTIPO_COMPRA,
                self::SUBTIPO_DEVOLUCION_CLIENTE,
                self::SUBTIPO_DEVOLUCION_PROVEEDOR,
                self::SUBTIPO_AJUSTE_INVENTARIO,
                self::SUBTIPO_TRANSFERENCIA_ENTRADA,
            ],
            self::TIPO_SALIDA => [
                self::SUBTIPO_VENTA,
                self::SUBTIPO_CONSUMO_PROYECTO,
                self::SUBTIPO_BAJA_OBSOLESCENCIA,
                self::SUBTIPO_TRANSFERENCIA_SALIDA,
                self::SUBTIPO_MERMA,
                self::SUBTIPO_ROBO_EXTRAVIO,
            ],
            self::TIPO_AJUSTE => [
                self::SUBTIPO_AJUSTE_INVENTARIO,
            ],
            self::TIPO_TRANSFERENCIA => [
                self::SUBTIPO_TRANSFERENCIA_ENTRADA,
                self::SUBTIPO_TRANSFERENCIA_SALIDA,
            ],
        ];
    }

    /**
     * Obtiene todos los estados posibles.
     *
     * @return array<string>
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDIENTE,
            self::STATUS_COMPLETADO,
            self::STATUS_CANCELADO,
        ];
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
     * Relación: un movimiento puede pertenecer a una ubicación.
     */
    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class);
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

    /**
     * Relación: obtener todos los movimientos del mismo número de movimiento.
     */
    public function movimientosAgrupados()
    {
        return $this->where('numero_movimiento', $this->numero_movimiento);
    }

    /**
     * Scopes para consultas frecuentes.
     */
    public function scopeEntrada($query)
    {
        return $query->where('tipo', self::TIPO_ENTRADA);
    }

    public function scopeSalida($query)
    {
        return $query->where('tipo', self::TIPO_SALIDA);
    }

    public function scopeAjuste($query)
    {
        return $query->where('tipo', self::TIPO_AJUSTE);
    }

    public function scopeTransferencia($query)
    {
        return $query->where('tipo', self::TIPO_TRANSFERENCIA);
    }

    public function scopePendientes($query)
    {
        return $query->where('status', self::STATUS_PENDIENTE);
    }

    public function scopeCompletados($query)
    {
        return $query->where('status', self::STATUS_COMPLETADO);
    }

    public function scopeCancelados($query)
    {
        return $query->where('status', self::STATUS_CANCELADO);
    }

    public function scopePorProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopePorProveedor($query, $proveedorId)
    {
        return $query->where('proveedor_id', $proveedorId);
    }

    public function scopePorProyecto($query, $proyectoId)
    {
        return $query->where('proyecto_id', $proyectoId);
    }

    public function scopePorUbicacion($query, $ubicacionId)
    {
        return $query->where('ubicacion_id', $ubicacionId);
    }

    public function scopePorNumeroMovimiento($query, $numeroMovimiento)
    {
        return $query->where('numero_movimiento', $numeroMovimiento);
    }

    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_hora', [$fechaInicio, $fechaFin]);
    }

    public function scopeConVencimiento($query)
    {
        return $query->whereNotNull('fecha_vencimiento');
    }

    public function scopeVencidos($query)
    {
        return $query->where('fecha_vencimiento', '<', now());
    }

    public function scopePorVencer($query, $dias = 30)
    {
        return $query->whereBetween('fecha_vencimiento', [
            now(),
            now()->addDays($dias)
        ]);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('numero_movimiento', 'LIKE', "%{$termino}%")
              ->orWhere('numero_documento', 'LIKE', "%{$termino}%")
              ->orWhere('numero_lote', 'LIKE', "%{$termino}%")
              ->orWhere('motivo', 'LIKE', "%{$termino}%");
        });
    }

    /**
     * Accessors y Mutators.
     */
    public function getEsEntradaAttribute(): bool
    {
        return $this->tipo === self::TIPO_ENTRADA;
    }

    public function getEsSalidaAttribute(): bool
    {
        return $this->tipo === self::TIPO_SALIDA;
    }

    public function getEsAjusteAttribute(): bool
    {
        return $this->tipo === self::TIPO_AJUSTE;
    }

    public function getEsTransferenciaAttribute(): bool
    {
        return $this->tipo === self::TIPO_TRANSFERENCIA;
    }

    public function getEstaPendienteAttribute(): bool
    {
        return $this->status === self::STATUS_PENDIENTE;
    }

    public function getEstaCompletadoAttribute(): bool
    {
        return $this->status === self::STATUS_COMPLETADO;
    }

    public function getEstaCanceladoAttribute(): bool
    {
        return $this->status === self::STATUS_CANCELADO;
    }

    public function getEstaVencidoAttribute(): bool
    {
        return $this->fecha_vencimiento && $this->fecha_vencimiento < now();
    }

    public function getDiasParaVencerAttribute(): ?int
    {
        if (!$this->fecha_vencimiento) {
            return null;
        }

        return now()->diffInDays($this->fecha_vencimiento, false);
    }

    public function getImpactoStockAttribute(): int
    {
        return match ($this->tipo) {
            self::TIPO_ENTRADA => (int) $this->cantidad,
            self::TIPO_SALIDA => -(int) $this->cantidad,
            self::TIPO_AJUSTE => $this->stock_posterior - $this->stock_anterior,
            self::TIPO_TRANSFERENCIA => 0, // Se maneja en ubicaciones específicas
            default => 0,
        };
    }

    public function getTipoFormateadoAttribute(): string
    {
        return ucfirst($this->tipo);
    }

    public function getSubtipoFormateadoAttribute(): ?string
    {
        if (!$this->subtipo) {
            return null;
        }

        return ucwords(str_replace('_', ' ', $this->subtipo));
    }

    /**
     * Métodos de utilidad.
     */
    public function puedeEditarse(): bool
    {
        return $this->status === self::STATUS_PENDIENTE;
    }

    public function puedeCancelarse(): bool
    {
        return $this->status === self::STATUS_PENDIENTE;
    }

    public function completar(): void
    {
        if ($this->status !== self::STATUS_PENDIENTE) {
            throw new \Exception('Solo se pueden completar movimientos pendientes');
        }

        $this->update(['status' => self::STATUS_COMPLETADO]);
        $this->actualizarStockProducto();
    }

    public function cancelar(): void
    {
        if ($this->status !== self::STATUS_PENDIENTE) {
            throw new \Exception('Solo se pueden cancelar movimientos pendientes');
        }

        $this->update(['status' => self::STATUS_CANCELADO]);
    }

    public function calcularCostoTotal(): void
    {
        if ($this->costo_unitario !== null) {
            $this->costo_total = $this->cantidad * $this->costo_unitario;
            $this->save();
        }
    }

    public function actualizarStockProducto(): void
    {
        if ($this->status !== self::STATUS_COMPLETADO) {
            return;
        }

        $producto = $this->producto;
        $impacto = $this->impacto_stock;

        // Actualizar stock del producto
        $nuevoStock = $producto->stock_actual + $impacto;
        $producto->update(['stock_actual' => max(0, $nuevoStock)]);

        // Actualizar totales de entradas/salidas
        if ($this->es_entrada) {
            $producto->increment('total_entradas', $this->cantidad);
        } elseif ($this->es_salida) {
            $producto->increment('total_salidas', $this->cantidad);
        }
    }

    public function obtenerMovimientosRelacionados()
    {
        return self::where('numero_movimiento', $this->numero_movimiento)
                  ->where('id', '!=', $this->id)
                  ->get();
    }

    /**
     * Genera un número de movimiento único.
     */
    public static function generarNumeroMovimiento(string $tipo): string
    {
        $prefijo = match ($tipo) {
            self::TIPO_ENTRADA => 'ENT',
            self::TIPO_SALIDA => 'SAL',
            self::TIPO_AJUSTE => 'AJU',
            self::TIPO_TRANSFERENCIA => 'TRA',
            default => 'MOV',
        };

        $fecha = now()->format('Ymd');
        $contador = 1;

        do {
            $numero = "{$prefijo}-{$fecha}-" . str_pad($contador, 4, '0', STR_PAD_LEFT);
            $existe = self::where('numero_movimiento', $numero)->exists();
            $contador++;
        } while ($existe);

        return $numero;
    }

    /**
     * Boot del modelo.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($movimiento) {
            // Generar número de movimiento automático si no se proporciona
            if (!$movimiento->numero_movimiento) {
                $movimiento->numero_movimiento = self::generarNumeroMovimiento($movimiento->tipo);
            }

            // Establecer fecha_hora por defecto
            if (!$movimiento->fecha_hora) {
                $movimiento->fecha_hora = now();
            }

            // Registrar stock anterior
            if ($movimiento->producto_id && !$movimiento->stock_anterior) {
                $producto = Producto::find($movimiento->producto_id);
                $movimiento->stock_anterior = $producto ? $producto->stock_actual : 0;
            }
        });

        static::saving(function ($movimiento) {
            // Validar cantidad positiva
            if ($movimiento->cantidad <= 0) {
                throw new \Exception('La cantidad debe ser mayor a cero');
            }

            // Validar coherencia entre tipo y subtipo
            $subtiposPorTipo = self::getSubtiposPorTipo();
            if ($movimiento->subtipo && isset($subtiposPorTipo[$movimiento->tipo])) {
                if (!in_array($movimiento->subtipo, $subtiposPorTipo[$movimiento->tipo])) {
                    throw new \Exception("El subtipo '{$movimiento->subtipo}' no es válido para el tipo '{$movimiento->tipo}'");
                }
            }

            // Calcular costo total automáticamente
            if ($movimiento->costo_unitario !== null && ($movimiento->isDirty('cantidad') || $movimiento->isDirty('costo_unitario'))) {
                $movimiento->costo_total = $movimiento->cantidad * $movimiento->costo_unitario;
            }

            // Calcular stock posterior para movimientos completados
            if ($movimiento->status === self::STATUS_COMPLETADO && $movimiento->stock_anterior !== null) {
                $impacto = match ($movimiento->tipo) {
                    self::TIPO_ENTRADA => (int) $movimiento->cantidad,
                    self::TIPO_SALIDA => -(int) $movimiento->cantidad,
                    default => 0,
                };
                $movimiento->stock_posterior = $movimiento->stock_anterior + $impacto;
            }
        });

        static::created(function ($movimiento) {
            // Actualizar stock del producto si el movimiento está completado
            if ($movimiento->status === self::STATUS_COMPLETADO) {
                $movimiento->actualizarStockProducto();
            }
        });
    }
}
