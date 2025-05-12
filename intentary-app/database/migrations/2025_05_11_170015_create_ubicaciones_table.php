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
        Schema::create('ubicaciones', function (Blueprint $table) {
            $table->id(); // id auto-incremental

            $table->string('codigo', 10)
                  ->unique()
                  ->comment('Código alfanumérico de la ubicación');

            $table->integer('nivel')
                  ->default(1)
                  ->comment('Nivel o piso de la ubicación en el almacén');

            $table->timestamps();

            // Índice adicional por nivel para filtrado rápido
            $table->index('nivel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ubicaciones');
    }
};
