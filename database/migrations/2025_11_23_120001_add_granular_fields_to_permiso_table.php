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
        Schema::table('permiso', function (Blueprint $table) {
            // Eliminar foreign keys antiguas que no vamos a usar
            $table->dropForeign(['id_configuracion']);
            $table->dropForeign(['id_bitacora']);

            // Eliminar columnas antiguas
            $table->dropColumn(['id_configuracion', 'id_bitacora']);

            // Agregar nuevos campos para sistema granular
            $table->string('modulo', 50)->after('nombre')->comment('Módulo del sistema (ej: compras, usuarios)');
            $table->string('accion', 50)->after('modulo')->comment('Acción permitida (ej: acceder, crear, editar)');
            $table->string('modificador', 50)->nullable()->after('accion')->comment('Modificador opcional (ej: sin_aprobacion)');
            $table->text('descripcion')->nullable()->after('modificador')->comment('Descripción del permiso');
            $table->timestamps(); // created_at, updated_at

            // Índice único para evitar permisos duplicados
            $table->unique(['modulo', 'accion', 'modificador'], 'permiso_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permiso', function (Blueprint $table) {
            // Eliminar índice único
            $table->dropUnique('permiso_unique');

            // Eliminar columnas nuevas
            $table->dropColumn(['modulo', 'accion', 'modificador', 'descripcion']);
            $table->dropTimestamps();

            // Restaurar columnas antiguas
            $table->unsignedBigInteger('id_configuracion')->nullable();
            $table->unsignedBigInteger('id_bitacora')->nullable();

            // Restaurar foreign keys
            $table->foreign('id_configuracion')->references('id')->on('configuracion')->onDelete('set null');
            $table->foreign('id_bitacora')->references('id')->on('bitacora')->onDelete('set null');
        });
    }
};
