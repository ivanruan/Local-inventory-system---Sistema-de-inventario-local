<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id(); // id auto-incremental

            // NUEVO: Número de movimiento para agrupar productos del mismo movimiento
            $table->string('numero_movimiento', 50)
                  ->comment('Número único del movimiento para agrupar múltiples productos');

            // Información básica del movimiento
            $table->dateTime('fecha_hora')
                  ->default(DB::raw('CURRENT_TIMESTAMP'))
                  ->comment('Fecha y hora del movimiento');
            
            $table->enum('tipo', ['entrada', 'salida', 'ajuste', 'transferencia'])
                  ->comment('Tipo de movimiento');
            
            $table->enum('subtipo', [
                'compra', 'devolucion_cliente', 'devolucion_proveedor', 'ajuste_inventario', 'transferencia_entrada',
                'venta', 'consumo_proyecto', 'baja_obsolescencia', 'transferencia_salida',
                'merma', 'robo_extravío'
            ])->nullable()->comment('Subtipo específico del movimiento');

            // Cantidad y costos
            $table->decimal('cantidad', 10, 3)
                  ->comment('Cantidad movida');
            $table->decimal('costo_unitario', 10, 2)->nullable()
                  ->comment('Costo unitario al momento del movimiento');
            $table->decimal('costo_total', 12, 2)->nullable()
                  ->comment('Costo total del movimiento');

            // Relaciones principales
            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // Relaciones opcionales
            $table->foreignId('proveedor_id')
                  ->nullable()
                  ->constrained('proveedores')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
            
            $table->foreignId('proyecto_id')
                  ->nullable()
                  ->constrained('proyectos')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->foreignId('ubicacion_id')
                  ->nullable()
                  ->constrained('ubicaciones')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            // Usuario responsable
            $table->foreignId('usuario_id')
                  ->constrained('usuarios') 
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            // Información de seguimiento
            $table->integer('stock_anterior')->nullable()
                  ->comment('Stock antes del movimiento');
            $table->integer('stock_posterior')->nullable()
                  ->comment('Stock después del movimiento');

            // Documentación
            $table->string('numero_documento', 100)->nullable()
                  ->comment('Número de factura, orden, etc.');
            $table->string('documento_soporte', 255)->nullable()
                  ->comment('Ruta del archivo de soporte');
            
            // Motivos y procedimientos
            $table->string('motivo', 200)->nullable()
                  ->comment('Motivo del movimiento');
            $table->text('procedimiento_disposicion')->nullable()
                  ->comment('Procedimiento de disposición posterior');

            // Campos específicos para ciertos tipos
            $table->decimal('tiempo_uso_acumulado', 10, 2)->nullable()
                  ->comment('Tiempo de uso acumulado (para equipos)');
            $table->date('fecha_vencimiento')->nullable()
                  ->comment('Fecha de vencimiento del lote');
            $table->string('numero_lote', 100)->nullable()
                  ->comment('Número de lote o serie');

            // Estado del movimiento
            $table->enum('status', ['pendiente', 'completado', 'cancelado'])
                  ->default('completado');

            // Observaciones generales
            $table->text('observaciones')->nullable();

            // Timestamps
            $table->timestamps();

            // Índices simples para búsquedas frecuentes
            $table->index('numero_movimiento'); // NUEVO índice
            $table->index('fecha_hora');
            $table->index('tipo');
            $table->index('subtipo');
            $table->index('producto_id');
            $table->index('usuario_id');
            $table->index('status');
            $table->index('numero_documento');
            $table->index('fecha_vencimiento');

            // Índices compuestos para consultas frecuentes
            $table->index(['numero_movimiento', 'producto_id']); // NUEVO: Productos por movimiento
            $table->index(['producto_id', 'fecha_hora']); // Historial de producto
            $table->index(['tipo', 'fecha_hora']); // Movimientos por tipo y fecha
            $table->index(['proyecto_id', 'fecha_hora']); // Movimientos por proyecto
            $table->index(['proveedor_id', 'tipo']); // Movimientos por proveedor
            $table->index(['usuario_id', 'fecha_hora']); // Actividad por usuario
            $table->index(['status', 'fecha_hora']); // Movimientos pendientes/completados
            $table->index(['ubicacion_id', 'tipo']); // Movimientos por ubicación
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};