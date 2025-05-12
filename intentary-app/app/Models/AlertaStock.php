<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AlertaStock
 *
 * @property int                             $id
 * @property int                             $producto_id
 * @property \Illuminate\Support\Carbon     $fecha_generacion
 * @property string                          $tipo_alerta
 * @property string                          $nivel_critico
 * @property bool                            $resuelta
 * @property \Illuminate\Support\Carbon|null $resuelta_en
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AlertaStock sinResolver()
 * @method static \Illuminate\Database\Eloquent\Builder|AlertaStock resueltas()
 */
class AlertaStock extends Model
{
    use HasFactory;

    // protected $table = 'alertas_stock';

    /**
     * Campos asignables masivamente.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'producto_id',
        'fecha_generacion',
        'tipo_alerta',
        'nivel_critico',
        'resuelta',
        'resuelta_en',
    ];

    /**
     * Casts de atributos.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'fecha_generacion' => 'datetime',
        'resuelta_en'      => 'datetime',
        'resuelta'         => 'boolean',
    ];

    /**
     * Scope para filtrar alertas no resueltas.
     */
    public function scopeSinResolver($query)
    {
        return $query->where('resuelta', false);
    }

    /**
     * Scope para filtrar alertas ya resueltas.
     */
    public function scopeResueltas($query)
    {
        return $query->where('resuelta', true);
    }

    /**
     * Relación: una alerta pertenece a un producto.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Marcar la alerta como resuelta.
     *
     * @param  \Illuminate\Support\Carbon|null  $fecha
     * @return void
     */
    public function marcarResuelta($fecha = null): void
    {
        $this->update([
            'resuelta'    => true,
            'resuelta_en' => $fecha ?? now(),
        ]);
    }
}
