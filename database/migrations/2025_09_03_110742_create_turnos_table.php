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
        Schema::create('turnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('servicio_id')->constrained('servicios')->onDelete('cascade');
            $table->foreignId('recurso_id')->constrained('recursos')->onDelete('cascade');
            $table->dateTime('fecha_hora_inicio');
            $table->dateTime('fecha_hora_fin');
            $table->integer('duracion_personalizada_minutos')->nullable(); // Permite sobrescribir duración del servicio
            $table->string('estado')->default('confirmado'); // confirmado, cancelado, completado
            $table->text('observaciones')->nullable();
            $table->decimal('precio_final', 10, 2)->nullable();
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['empresa_id', 'fecha_hora_inicio']);
            $table->index(['recurso_id', 'fecha_hora_inicio', 'fecha_hora_fin']);
            $table->index(['cliente_id', 'fecha_hora_inicio']);
            $table->index(['estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turnos');
    }
};
