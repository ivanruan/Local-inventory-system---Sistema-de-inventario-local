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
        Schema::create('adjuntos', function (Blueprint $table) {
            $table->id(); // id auto-incremental

            // Relación con movimientos_inventario
            $table->foreignId('movimiento_id')
                  ->constrained('movimientos_inventario')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // Datos del adjunto
            $table->string('tipo', 50)
                  ->comment('Tipo de adjunto (por ejemplo, factura, imagen, PDF)');
            $table->string('ruta_archivo')
                  ->comment('Ruta en el sistema de archivos o URL del adjunto');
            $table->text('descripcion')
                  ->nullable()
                  ->comment('Descripción adicional del adjunto');

            // Timestamps
            $table->timestamps();

            // Índice para consultas por movimiento
            $table->index('movimiento_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adjuntos');
    }
};
