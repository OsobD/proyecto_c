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
            // Eliminar la foreign key antigua que apunta a 'users'
            $table->dropForeign('bitacora_id_usuario_foreign');

            // Crear la nueva foreign key que apunta a 'usuario'
            $table->foreign('id_usuario')->references('id')->on('usuario')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bitacora', function (Blueprint $table) {
            // Eliminar la foreign key que apunta a 'usuario'
            $table->dropForeign('bitacora_id_usuario_foreign');

            // Restaurar la foreign key que apunta a 'users'
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('set null');
        });
    }
};
