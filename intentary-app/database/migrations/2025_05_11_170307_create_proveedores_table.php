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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id(); // id auto-incremental

            // Campo obligatorio
            $table->string('nombre', 150)
                  ->comment('Nombre del proveedor');

            // Campos opcionales - Información básica
            $table->string('codigo', 50)->nullable()->unique();
            $table->string('razon_social', 200)->nullable();
            
            // Información de contacto
            $table->string('telefono', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('direccion', 250)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('estado', 100)->nullable();
            $table->string('codigo_postal', 10)->nullable();
            $table->string('pais', 100)->nullable()->default('México');
            
            // Información fiscal
            $table->string('rfc', 15)->nullable();
            $table->string('contacto_principal', 100)->nullable();
            $table->string('puesto_contacto', 100)->nullable();
            
            // Términos comerciales
            $table->integer('dias_credito')->nullable();
            $table->decimal('limite_credito', 15, 2)->nullable();
            $table->string('moneda', 10)->nullable()->default('MXN');
            $table->string('forma_pago', 50)->nullable();
            
            // Estado del proveedor
            $table->enum('status', ['Activo', 'Inactivo', 'Bloqueado'])
                  ->default('Activo');
            
            // Información adicional
            $table->text('observaciones')->nullable();
            $table->string('sitio_web', 255)->nullable();
            $table->decimal('calificacion', 3, 2)->nullable()
                  ->comment('Calificación del proveedor (0.00 - 10.00)');

            $table->timestamps();
            
            // Índices para búsquedas frecuentes
            $table->index('nombre');
            $table->index('status');
            $table->index('codigo');
            $table->index('rfc');
            $table->index('ciudad');
            
            // Índices compuestos
            $table->index(['status', 'calificacion']); // Proveedores activos ordenados por calificación
            $table->index(['ciudad', 'status']); // Proveedores por ciudad y estado
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};