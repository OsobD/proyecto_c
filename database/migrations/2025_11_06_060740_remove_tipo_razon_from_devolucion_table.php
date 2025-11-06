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
        Schema::table('devolucion', function (Blueprint $table) {
            // Eliminar foreign keys primero
            $table->dropForeign(['id_tipo_devolucion']);
            $table->dropForeign(['id_razon_devolucion']);

            // Eliminar columnas
            $table->dropColumn(['id_tipo_devolucion', 'id_razon_devolucion']);
        });

        // Eliminar columnas de detalle_devolucion
        Schema::table('detalle_devolucion', function (Blueprint $table) {
            $table->dropColumn(['estado_producto', 'precio_unitario']);
        });

        // Eliminar tablas
        Schema::dropIfExists('tipo_devolucion');
        Schema::dropIfExists('razon_devolucion');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recrear tablas
        Schema::create('tipo_devolucion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        Schema::create('razon_devolucion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        // Agregar columnas de vuelta
        Schema::table('devolucion', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tipo_devolucion')->nullable();
            $table->unsignedBigInteger('id_razon_devolucion')->nullable();

            $table->foreign('id_tipo_devolucion')->references('id')->on('tipo_devolucion')->onDelete('set null');
            $table->foreign('id_razon_devolucion')->references('id')->on('razon_devolucion')->onDelete('set null');
        });

        Schema::table('detalle_devolucion', function (Blueprint $table) {
            $table->string('estado_producto')->nullable();
            $table->double('precio_unitario')->nullable();
        });
    }
};
