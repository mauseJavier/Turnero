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
        Schema::create('recursos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('tipo'); // ej: "cancha", "sala", "silla", "consultorio"
            $table->integer('capacidad')->default(1);
            $table->boolean('activo')->default(true);
            $table->time('inicio_turno')->nullable(); // Campo para el inicio de turno
            $table->timestamps();
            
            $table->index(['empresa_id', 'tipo', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recursos');
    }
};
