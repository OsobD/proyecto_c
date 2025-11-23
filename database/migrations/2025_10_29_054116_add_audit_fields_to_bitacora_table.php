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
        Schema::table('bitacora', function (Blueprint $table) {
            $table->string('accion')->after('id'); // 'crear', 'editar', 'eliminar', 'login', etc.
            $table->string('modelo')->after('accion'); // 'Bodega', 'TarjetaResponsabilidad', etc.
            $table->unsignedBigInteger('modelo_id')->nullable()->after('modelo'); // ID del registro afectado
            $table->text('descripcion')->nullable()->after('modelo_id'); // Descripción legible
            $table->json('datos_anteriores')->nullable()->after('descripcion'); // Estado antes del cambio
            $table->json('datos_nuevos')->nullable()->after('datos_anteriores'); // Estado después del cambio
            $table->unsignedBigInteger('id_usuario')->nullable()->after('datos_nuevos'); // Quien hizo la acción
            $table->string('ip_address', 45)->nullable()->after('id_usuario');
            $table->string('user_agent')->nullable()->after('ip_address');
            $table->timestamp('created_at')->after('user_agent');

            // Foreign key
            $table->foreign('id_usuario')->references('id')->on('usuario')->onDelete('set null');

            // Índices para mejorar rendimiento de consultas
            $table->index(['modelo', 'modelo_id']);
            $table->index('accion');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bitacora', function (Blueprint $table) {
            // Eliminar foreign key primero
            $table->dropForeign(['id_usuario']);

            // Eliminar índices
            $table->dropIndex(['modelo', 'modelo_id']);
            $table->dropIndex(['accion']);
            $table->dropIndex(['created_at']);

            // Eliminar columnas
            $table->dropColumn([
                'accion',
                'modelo',
                'modelo_id',
                'descripcion',
                'datos_anteriores',
                'datos_nuevos',
                'id_usuario',
                'ip_address',
                'user_agent',
                'created_at'
            ]);
        });
    }
};
