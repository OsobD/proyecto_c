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
        // Actualizar foreign keys de tarjeta_responsabilidad para apuntar a 'usuario' en lugar de 'users'

        Schema::table('tarjeta_responsabilidad', function (Blueprint $table) {
            // Eliminar las foreign keys existentes
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Crear nuevas foreign keys apuntando a la tabla 'usuario'
            $table->foreign('created_by')->references('id')->on('usuario')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('usuario')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a foreign keys hacia 'users'

        Schema::table('tarjeta_responsabilidad', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }
};
