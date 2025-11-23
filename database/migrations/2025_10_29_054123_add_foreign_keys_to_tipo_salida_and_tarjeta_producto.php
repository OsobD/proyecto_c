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
        // Agregar foreign key a tipo_salida (para id_salida)
        Schema::table('tipo_salida', function (Blueprint $table) {
            $table->foreign('id_salida')->references('id')->on('salida')->onDelete('set null');
        });

        // Agregar foreign keys a traslado (para id_tarjeta apuntando a tarjeta_producto)
        Schema::table('traslado', function (Blueprint $table) {
            $table->foreign('id_tarjeta')->references('id')->on('tarjeta_producto')->onDelete('set null');
        });

        // Agregar foreign keys a devolucion (para id_tarjeta apuntando a tarjeta_producto)
        Schema::table('devolucion', function (Blueprint $table) {
            $table->foreign('id_tarjeta')->references('id')->on('tarjeta_producto')->onDelete('set null');
        });

        // Agregar foreign keys a salida (para id_tarjeta apuntando a tarjeta_producto)
        Schema::table('salida', function (Blueprint $table) {
            $table->foreign('id_tarjeta')->references('id')->on('tarjeta_producto')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salida', function (Blueprint $table) {
            $table->dropForeign(['id_tarjeta']);
        });

        Schema::table('devolucion', function (Blueprint $table) {
            $table->dropForeign(['id_tarjeta']);
        });

        Schema::table('traslado', function (Blueprint $table) {
            $table->dropForeign(['id_tarjeta']);
        });

        Schema::table('tipo_salida', function (Blueprint $table) {
            $table->dropForeign(['id_salida']);
        });
    }
};
