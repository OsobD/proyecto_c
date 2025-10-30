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
        // Agregar campo activo a tabla bodega
        Schema::table('bodega', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('nombre');
        });

        // Agregar campo activo a tabla tarjeta_responsabilidad
        Schema::table('tarjeta_responsabilidad', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('id_persona');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bodega', function (Blueprint $table) {
            $table->dropColumn('activo');
        });

        Schema::table('tarjeta_responsabilidad', function (Blueprint $table) {
            $table->dropColumn('activo');
        });
    }
};
