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
            $table->string('especificacion', 100);

            // Relaciones
            $table->foreignId('marca_id')
                  ->nullable()
                  ->constrained('marcas')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->foreignId('categoria_id')
                  ->constrained('categorias')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->foreignId('ubicacion_id')
                  ->constrained('ubicaciones')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->foreignId('proveedor_id')
                  ->nullable()
                  ->constrained('proveedores')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->string('unidad', 20);
            $table->integer('nivel')->default(0);

            // Stocks - incluyendo stock inicial, entradas y salidas
            $table->integer('stock_inicial')->default(0);
            $table->integer('total_entradas')->default(0);
            $table->integer('total_salidas')->default(0);
            $table->integer('stock_minimo')->nullable();
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_maximo')->nullable();
            $table->integer('stock_seguridad')->nullable();

            $table->integer('duracion_inventario')->nullable()
                  ->comment('Duración en inventario en días');

            // Estado con enum ampliado
            $table->enum('status', [
                'Stock Optimo',
                'Stock Bajo',
                'Fuera de Stock',
                'Sobre Stock'
            ])->default('Fuera de Stock');

            // Costos y vida útil
            $table->decimal('valor_unitario', 20, 2)->default(0.00);
            $table->integer('vida_util')->nullable()
                  ->comment('Vida útil en días');

            $table->text('observaciones')->nullable();

            // Timestamps
            $table->timestamps();

            // Índices adicionales para búsquedas frecuentes
            $table->index('nombre');
            $table->index('codigo');
            $table->index('status');
            $table->index('especificacion');
            $table->index('marca_id');     // Para filtros por marca
            $table->index('categoria_id'); // Para filtros por categoría
            $table->index('ubicacion_id'); // Para filtros por ubicación
            $table->index('nivel');
            $table->index(['stock_actual', 'stock_minimo']); // Para consultas de stock bajo
            
            // Índices compuestos para consultas frecuentes combinadas
            $table->index(['categoria_id', 'ubicacion_id']); // Productos por categoría en ubicación específica
            $table->index(['marca_id', 'status']); // Productos de marca específica por estado
            $table->index(['categoria_id', 'status']); // Productos activos/inactivos por categoría
            $table->index(['ubicacion_id', 'status']); // Productos activos/inactivos por ubicación
            $table->index(['status', 'stock_actual']); // Estados de stock con cantidad actual
            $table->index(['categoria_id', 'marca_id']); // Productos por categoría y marca
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