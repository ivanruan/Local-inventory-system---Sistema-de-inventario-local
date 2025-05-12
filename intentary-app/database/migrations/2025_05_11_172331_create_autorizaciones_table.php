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
        Schema::create('autorizaciones', function (Blueprint $table) {
            $table->id(); // id auto-incremental

            // Relación con movimientos_inventario
            $table->foreignId('movimiento_id')
                  ->constrained('movimientos_inventario')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // Datos de autorización
            $table->string('autorizador_nombre', 100)
                  ->comment('Nombre de quien autoriza');
            $table->string('autorizador_cargo', 100)
                  ->nullable()
                  ->comment('Cargo del autorizador');
            $table->string('firma_url', 200)
                  ->nullable()
                  ->comment('Ruta de la firma digital o imagen');
            $table->dateTime('fecha_autorizacion')
                  ->comment('Fecha y hora de la autorización');
            $table->text('observaciones')
                  ->nullable()
                  ->comment('Observaciones adicionales');

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
        Schema::dropIfExists('autorizaciones');
    }
};
