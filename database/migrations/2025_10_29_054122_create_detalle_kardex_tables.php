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
        // Tabla de detalle genérico
        Schema::create('detalle', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tipo')->nullable();
            $table->unsignedBigInteger('id_det_compra')->nullable();
            $table->unsignedBigInteger('id_det_entrada')->nullable();
            $table->unsignedBigInteger('id_det_devolucion')->nullable();
            $table->unsignedBigInteger('id_det_traslado')->nullable();
            $table->unsignedBigInteger('id_det_salida')->nullable();

            $table->foreign('id_tipo')->references('id')->on('tipo_transacion')->onDelete('set null');
            $table->foreign('id_det_compra')->references('id')->on('detalle_compra')->onDelete('cascade');
            $table->foreign('id_det_entrada')->references('id')->on('detalle_entrada')->onDelete('cascade');
            $table->foreign('id_det_devolucion')->references('id')->on('detalle_devolucion')->onDelete('cascade');
            $table->foreign('id_det_traslado')->references('id')->on('detalle_traslado')->onDelete('cascade');
            $table->foreign('id_det_salida')->references('id')->on('detalle_salida')->onDelete('cascade');
        });

        // Tabla de kardex
        Schema::create('kardex', function (Blueprint $table) {
            $table->id();
            $table->datetime('timestamp');
            $table->string('tipo_movimiento');
            $table->unsignedBigInteger('id_detalle')->nullable();

            $table->foreign('id_detalle')->references('id')->on('detalle')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kardex');
        Schema::dropIfExists('detalle');
    }
};
