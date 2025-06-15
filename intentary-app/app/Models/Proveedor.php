<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Proveedor
 *
 * @property int         $id
 * @property string      $nombre
 * @property string|null $codigo
 * @property string|null $razon_social
 * @property string|null $telefono
 * @property string|null $email
 * @property string|null $direccion
 * @property string|null $ciudad
 * @property string|null $estado
 * @property string|null $codigo_postal
 * @property string|null $pais
 * @property string|null $rfc
 * @property string|null $contacto_principal
 * @property string|null $puesto_contacto
 * @property int|null    $dias_credito
 * @property float|null  $limite_credito
 * @property string|null $moneda
 * @property string|null $forma_pago
 * @property string      $status
 * @property string|null $observaciones
 * @property string|null $sitio_web
 * @property float|null  $calificacion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    /**
     * Campos que se pueden asignar masivamente.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'nombre',
        'codigo',
        'razon_social',
        'telefono',
        'email',
        'direccion',
        'ciudad',
        'estado',
        'codigo_postal',
        'pais',
        'rfc',
        'contacto_principal',
        'puesto_contacto',
        'dias_credito',
        'limite_credito',
        'moneda',
        'forma_pago',
        'status',
        'observaciones',
        'sitio_web',
        'calificacion',
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'dias_credito'   => 'integer',
        'limite_credito' => 'decimal:2',
        'calificacion'   => 'decimal:2',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'status'         => 'string',
    ];

    /**
     * Valores por defecto para atributos.
     *
     * @var array<string,mixed>
     */
    protected $attributes = [
        'pais'   => 'México',
        'moneda' => 'MXN',
        'status' => 'Activo',
    ];

    /**
     * Constantes para los estados del proveedor.
     */
    public const STATUS_ACTIVO = 'Activo';
    public const STATUS_INACTIVO = 'Inactivo';
    public const STATUS_BLOQUEADO = 'Bloqueado';

    /**
     * Constantes para formas de pago comunes.
     */
    public const FORMA_PAGO_EFECTIVO = 'Efectivo';
    public const FORMA_PAGO_TRANSFERENCIA = 'Transferencia';
    public const FORMA_PAGO_CHEQUE = 'Cheque';
    public const FORMA_PAGO_TARJETA = 'Tarjeta de Crédito';

    /**
     * Constantes para monedas comunes.
     */
    public const MONEDA_MXN = 'MXN';
    public const MONEDA_USD = 'USD';
    public const MONEDA_EUR = 'EUR';

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
            self::STATUS_BLOQUEADO,
        ];
    }

    /**
     * Obtiene todas las formas de pago comunes.
     *
     * @return array<string>
     */
    public static function getFormasPagoOptions(): array
    {
        return [
            self::FORMA_PAGO_EFECTIVO,
            self::FORMA_PAGO_TRANSFERENCIA,
            self::FORMA_PAGO_CHEQUE,
            self::FORMA_PAGO_TARJETA,
        ];
    }

    /**
     * Obtiene todas las monedas disponibles.
     *
     * @return array<string>
     */
    public static function getMonedasOptions(): array
    {
        return [
            self::MONEDA_MXN => 'Peso Mexicano',
            self::MONEDA_USD => 'Dólar Americano',
            self::MONEDA_EUR => 'Euro',
        ];
    }

    /**
     * Relación: un Proveedor puede tener muchos Productos.
     */
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    /**
     * Relación: un Proveedor puede tener muchas Órdenes de Compra.
     */
    public function ordenesCompra()
    {
        return $this->hasMany(OrdenCompra::class);
    }

    /**
     * Relación: un Proveedor puede tener muchas Facturas.
     */
    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    /**
     * Scopes para consultas frecuentes.
     */
    public function scopeActivos($query)
    {
        return $query->where('status', self::STATUS_ACTIVO);
    }

    public function scopeInactivos($query)
    {
        return $query->where('status', self::STATUS_INACTIVO);
    }

    public function scopeBloqueados($query)
    {
        return $query->where('status', self::STATUS_BLOQUEADO);
    }

    public function scopePorCiudad($query, $ciudad)
    {
        return $query->where('ciudad', $ciudad);
    }

    public function scopePorEstado($query, $state)
    {
        return $query->where('estado', $state);
    }

    public function scopePorCalificacion($query, $minCalificacion = null)
    {
        $query = $query->whereNotNull('calificacion');
        
        if ($minCalificacion !== null) {
            $query->where('calificacion', '>=', $minCalificacion);
        }
        
        return $query->orderBy('calificacion', 'desc');
    }

    public function scopeConCredito($query)
    {
        return $query->whereNotNull('dias_credito')
                    ->where('dias_credito', '>', 0);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'LIKE', "%{$termino}%")
              ->orWhere('razon_social', 'LIKE', "%{$termino}%")
              ->orWhere('codigo', 'LIKE', "%{$termino}%")
              ->orWhere('rfc', 'LIKE', "%{$termino}%");
        });
    }

    /**
     * Accessors y Mutators.
     */
    public function getNombreCompletoAttribute(): string
    {
        if ($this->razon_social && $this->razon_social !== $this->nombre) {
            return "{$this->nombre} ({$this->razon_social})";
        }
        
        return $this->nombre;
    }

    public function getDireccionCompletaAttribute(): ?string
    {
        $partes = array_filter([
            $this->direccion,
            $this->ciudad,
            $this->estado,
            $this->codigo_postal,
            $this->pais
        ]);

        return !empty($partes) ? implode(', ', $partes) : null;
    }

    public function getEsActivoAttribute(): bool
    {
        return $this->status === self::STATUS_ACTIVO;
    }

    public function getEsBloqueadoAttribute(): bool
    {
        return $this->status === self::STATUS_BLOQUEADO;
    }

    public function getTieneCreditorAttribute(): bool
    {
        return $this->dias_credito !== null && $this->dias_credito > 0;
    }

    public function getCalificacionEstrellas(): string
    {
        if ($this->calificacion === null) {
            return 'Sin calificar';
        }

        $estrellas = round($this->calificacion / 2); // Convertir de 0-10 a 0-5 estrellas
        return str_repeat('★', $estrellas) . str_repeat('☆', 5 - $estrellas);
    }

    public function setRfcAttribute($value): void
    {
        $this->attributes['rfc'] = $value ? strtoupper(trim($value)) : null;
    }

    public function setEmailAttribute($value): void
    {
        $this->attributes['email'] = $value ? strtolower(trim($value)) : null;
    }

    public function setCodigoPostalAttribute($value): void
    {
        $this->attributes['codigo_postal'] = $value ? preg_replace('/\D/', '', $value) : null;
    }

    /**
     * Métodos de utilidad.
     */
    public function puedeComprar(): bool
    {
        return $this->status === self::STATUS_ACTIVO;
    }

    public function validarLimiteCredito(float $monto): bool
    {
        if ($this->limite_credito === null) {
            return true; // Sin límite establecido
        }

        return $monto <= $this->limite_credito;
    }

    public function calcularPromedioCalificaciones(): void
    {
        // Este método podría calcular la calificación promedio
        // basada en evaluaciones o órdenes de compra
        // Por ahora es un placeholder para implementación futura
    }

    public function obtenerContactoCompleto(): ?string
    {
        if (!$this->contacto_principal) {
            return null;
        }

        $contacto = $this->contacto_principal;
        
        if ($this->puesto_contacto) {
            $contacto .= " - {$this->puesto_contacto}";
        }

        return $contacto;
    }

    /**
     * Boot del modelo.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($proveedor) {
            // Generar código automático si no se proporciona
            if (!$proveedor->codigo) {
                $proveedor->codigo = self::generarCodigo($proveedor->nombre);
            }
        });

        static::saving(function ($proveedor) {
            // Validar calificación esté en rango válido
            if ($proveedor->calificacion !== null && 
                ($proveedor->calificacion < 0 || $proveedor->calificacion > 10)) {
                throw new \Exception('La calificación debe estar entre 0.00 y 10.00');
            }

            // Validar días de crédito
            if ($proveedor->dias_credito !== null && $proveedor->dias_credito < 0) {
                throw new \Exception('Los días de crédito no pueden ser negativos');
            }

            // Validar límite de crédito
            if ($proveedor->limite_credito !== null && $proveedor->limite_credito < 0) {
                throw new \Exception('El límite de crédito no puede ser negativo');
            }

            // Validar formato de RFC (básico)
            if ($proveedor->rfc && !preg_match('/^[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}$/', $proveedor->rfc)) {
                throw new \Exception('El formato del RFC no es válido');
            }

            // Validar email
            if ($proveedor->email && !filter_var($proveedor->email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('El formato del email no es válido');
            }
        });
    }

    /**
     * Genera un código automático basado en el nombre.
     */
    private static function generarCodigo(string $nombre): string
    {
        // Tomar las primeras 3 letras del nombre, convertir a mayúsculas
        $codigo = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $nombre), 0, 3));
        
        // Agregar número secuencial
        $contador = 1;
        $codigoBase = $codigo;
        
        while (self::where('codigo', $codigo)->exists()) {
            $codigo = $codigoBase . str_pad($contador, 3, '0', STR_PAD_LEFT);
            $contador++;
        }
        
        return $codigo;
    }
}
