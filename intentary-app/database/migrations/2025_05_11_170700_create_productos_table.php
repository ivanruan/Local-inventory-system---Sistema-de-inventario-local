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
        Schema::create('productos', function (Blueprint $table) {
            $table->id(); // id auto-incremental

            $table->string('codigo', 100)->unique();
            $table->string('nombre', 100);
            $table->string('especificacion', 200)->nullable();

            // Relaciones
            $table->foreignId('marca_id')
                  ->constrained('marcas')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->foreignId('categoria_id')
                  ->constrained('categorias')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->foreignId('ubicacion_id')
                  ->constrained('ubicaciones')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->string('unidad', 20);
            $table->integer('nivel')->default(0);

            // Stocks
            $table->integer('stock_minimo')->default(0);
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_maximo')->default(0);
            $table->integer('stock_seguridad')->default(0);

            $table->integer('duracion_inventario')->default(0)
                  ->comment('Duración en inventario en días');

            // Estado con enum
            $table->enum('status', ['Activo', 'Inactivo', 'Obsoleto'])
                  ->default('Activo');

            // Costos y vida útil
            $table->decimal('costo', 10, 2)->default(0.00);
            $table->integer('vida_util')->default(0)
                  ->comment('Vida útil en días');

            $table->text('observaciones')->nullable();

            // Timestamps
            $table->timestamps();

            // Índice adicional para búsquedas frecuentes
            $table->index('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
