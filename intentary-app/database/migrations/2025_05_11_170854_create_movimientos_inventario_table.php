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

            // Fecha y tipo de movimiento
            $table->dateTime('fecha_hora')
                  ->default(DB::raw('CURRENT_TIMESTAMP'))
                  ->comment('Fecha y hora del movimiento');
            $table->enum('tipo', ['entrada', 'salida'])
                  ->comment('Tipo de movimiento: entrada o salida');

            // Cantidad y producto relacionado
            $table->decimal('cantidad', 10, 2)
                  ->comment('Cantidad movida');
            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // Proveedor y proyecto (opcionales)
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

            // Usuario que registra el movimiento
            $table->foreignId('usuario_id')
                  ->constrained('usuarios')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            // Datos específicos adicionales
            $table->decimal('tiempo_uso_acumulado', 10, 2)
                  ->default(0.00)
                  ->comment('Tiempo de uso acumulado del producto');
            $table->string('documento_ingreso')
                  ->nullable()
                  ->comment('Ruta o identificador del documento de ingreso');
            $table->text('motivo_salida')
                  ->nullable()
                  ->comment('Motivo de la salida');
            $table->text('procedimiento_disposicion')
                  ->nullable()
                  ->comment('Procedimiento de disposición posterior');

            // Observaciones generales
            $table->text('observaciones')
                  ->nullable();

            // Timestamps
            $table->timestamps();

            // Índices para búsquedas frecuentes
            $table->index('fecha_hora');
            $table->index('tipo');
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
