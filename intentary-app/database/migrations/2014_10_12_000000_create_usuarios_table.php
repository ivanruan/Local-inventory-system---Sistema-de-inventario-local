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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
	    $table->string('password', 255)
		  ->comment('Password hasheada');

	    $table->enum('rol', ['admin', 'operador', 'supervisor'])
                  ->default('operador')
                  ->comment('Rol del usuario en el sistema');
            $table->boolean('activo')
                  ->default(true)
                  ->comment('Si el usuario está activo');
	    
	    
	    $table->rememberToken();
	    $table->timestamps();

	    // Índices
            $table->index('rol');
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
