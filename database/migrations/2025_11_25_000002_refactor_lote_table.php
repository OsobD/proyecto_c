<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Refactoriza la tabla lote para que sea independiente de la bodega.
     * El campo 'cantidad' ahora representa la cantidad total disponible del lote
     * independientemente de en qué bodega esté.
     */
    public function up(): void
    {
        Schema::table('lote', function (Blueprint $table) {
            // Renombrar cantidad a cantidad_disponible para mayor claridad
            $table->renameColumn('cantidad', 'cantidad_disponible');
        });

        // Hacer que id_bodega sea nullable (mantener temporalmente para compatibilidad)
        // En una futura migración se eliminará completamente
        Schema::table('lote', function (Blueprint $table) {
            $table->unsignedBigInteger('id_bodega')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lote', function (Blueprint $table) {
            $table->unsignedBigInteger('id_bodega')->nullable(false)->change();
        });

        Schema::table('lote', function (Blueprint $table) {
            $table->renameColumn('cantidad_disponible', 'cantidad');
        });
    }
};
