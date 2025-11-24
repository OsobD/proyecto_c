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
        Schema::create('cambios_pendientes', function (Blueprint $table) {
            $table->id();

            // Información del cambio
            $table->string('modelo', 100)->comment('Modelo afectado (ej: Compra, Usuario)');
            $table->unsignedBigInteger('modelo_id')->nullable()->comment('ID del registro (null si es creación)');
            $table->enum('accion', ['crear', 'editar', 'eliminar'])->comment('Tipo de acción solicitada');

            // Datos del cambio
            $table->json('datos_anteriores')->nullable()->comment('Datos originales (solo en edición)');
            $table->json('datos_nuevos')->comment('Datos nuevos/propuestos');

            // Información de aprobación
            $table->unsignedBigInteger('usuario_solicitante_id')->comment('Quién solicitó el cambio');
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->unsignedBigInteger('usuario_aprobador_id')->nullable()->comment('Quién aprobó/rechazó');
            $table->timestamp('fecha_aprobacion')->nullable()->comment('Cuándo se aprobó/rechazó');

            // Justificación y observaciones
            $table->text('justificacion')->nullable()->comment('Justificación del solicitante');
            $table->text('observaciones')->nullable()->comment('Comentarios del aprobador');

            $table->timestamps(); // created_at (cuándo se solicitó), updated_at

            // Foreign keys
            $table->foreign('usuario_solicitante_id')
                  ->references('id')
                  ->on('usuario')
                  ->onDelete('cascade');

            $table->foreign('usuario_aprobador_id')
                  ->references('id')
                  ->on('usuario')
                  ->onDelete('set null');

            // Índices para mejorar rendimiento
            $table->index(['estado', 'created_at']);
            $table->index(['modelo', 'modelo_id']);
            $table->index('usuario_solicitante_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cambios_pendientes');
    }
};
