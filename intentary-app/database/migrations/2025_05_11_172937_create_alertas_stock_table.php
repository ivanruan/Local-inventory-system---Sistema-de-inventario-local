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
        Schema::create('alertas_stock', function (Blueprint $table) {
            $table->id(); // id auto-incremental

            // Relación con productos
            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->dateTime('fecha_generacion')
                  ->default(DB::raw('CURRENT_TIMESTAMP'))
                  ->comment('Fecha y hora de generación de la alerta');
            $table->enum('tipo_alerta', ['Stock Bajo', 'Stock Excedido', 'Vida Útil Límite'])
                  ->comment('Tipo de alerta');
            $table->enum('nivel_critico', ['Alto', 'Medio', 'Bajo'])
                  ->comment('Nivel crítico de la alerta');
            $table->boolean('resuelta')
                  ->default(false)
                  ->comment('Indica si la alerta ha sido atendida');
            $table->dateTime('resuelta_en')
                  ->nullable()
                  ->comment('Fecha y hora en que se resolvió la alerta');

            // Timestamps
            $table->timestamps();

            // Índices para optimización
            $table->index('producto_id');
            $table->index('tipo_alerta');
            $table->index('nivel_critico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alertas_stock');
    }
};
