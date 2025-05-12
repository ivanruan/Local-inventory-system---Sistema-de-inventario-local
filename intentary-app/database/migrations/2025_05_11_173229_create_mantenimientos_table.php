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
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id(); // id auto-incremental

            // Relación con productos
            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // Fechas
            $table->dateTime('fecha_programada')
                  ->comment('Fecha y hora programada para el mantenimiento');
            $table->dateTime('fecha_ejecucion')
                  ->nullable()
                  ->comment('Fecha y hora en que se ejecutó el mantenimiento');

            // Tipo y estado del mantenimiento
            $table->enum('tipo', ['preventivo', 'correctivo', 'limpieza'])
                  ->comment('Tipo de mantenimiento');
            $table->enum('status', ['pendiente', 'completado', 'cancelado'])
                  ->comment('Estado del mantenimiento');

            // Detalles adicionales
            $table->text('descripcion')
                  ->nullable()
                  ->comment('Descripción del mantenimiento');
            $table->string('responsable', 100)
                  ->comment('Persona responsable del mantenimiento');
            $table->decimal('costo', 10, 2)
                  ->nullable()
                  ->comment('Costo del mantenimiento');
            $table->text('observaciones')
                  ->nullable()
                  ->comment('Observaciones adicionales');

            // Timestamps
            $table->timestamps();

            // Índices
            $table->index('producto_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};
