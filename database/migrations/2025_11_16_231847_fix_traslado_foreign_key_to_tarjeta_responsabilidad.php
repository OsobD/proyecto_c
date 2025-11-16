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
        // Eliminar foreign key incorrecta de traslado que apunta a tarjeta_producto
        Schema::table('traslado', function (Blueprint $table) {
            $table->dropForeign(['id_tarjeta']);
        });

        // Agregar foreign key correcta apuntando a tarjeta_responsabilidad
        Schema::table('traslado', function (Blueprint $table) {
            $table->foreign('id_tarjeta')->references('id')->on('tarjeta_responsabilidad')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar la foreign key correcta
        Schema::table('traslado', function (Blueprint $table) {
            $table->dropForeign(['id_tarjeta']);
        });

        // Restaurar la foreign key incorrecta (para poder hacer rollback)
        Schema::table('traslado', function (Blueprint $table) {
            $table->foreign('id_tarjeta')->references('id')->on('tarjeta_producto')->onDelete('set null');
        });
    }
};
