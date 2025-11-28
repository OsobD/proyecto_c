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
        Schema::table('usuario', function (Blueprint $table) {
            // Eliminar foreign key y columna id_puesto
            $table->dropForeign(['id_puesto']);
            $table->dropColumn('id_puesto');
            
            // Agregar campo para forzar cambio de contraseña en primer login
            $table->boolean('debe_cambiar_contrasena')->default(true)->after('contrasena');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            // Restaurar columna id_puesto
            $table->unsignedBigInteger('id_puesto')->nullable()->after('id_rol');
            $table->foreign('id_puesto')->references('id')->on('puesto')->onDelete('set null');
            
            // Eliminar campo debe_cambiar_contrasena
            $table->dropColumn('debe_cambiar_contrasena');
        });
    }
};
