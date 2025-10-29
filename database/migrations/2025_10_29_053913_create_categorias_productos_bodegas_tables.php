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
        // Tabla de categorías
        Schema::create('categoria', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
        });

        // Tabla de bodegas
        Schema::create('bodega', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
        });

        // Tabla de productos (ID es string, no auto-incrementable)
        Schema::create('producto', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('id_categoria')->nullable();

            $table->foreign('id_categoria')->references('id')->on('categoria')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto');
        Schema::dropIfExists('bodega');
        Schema::dropIfExists('categoria');
    }
};
