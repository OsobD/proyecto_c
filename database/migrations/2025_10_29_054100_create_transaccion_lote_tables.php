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
        // Tabla de transacciones
        Schema::create('transaccion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tipo')->nullable();
            $table->unsignedBigInteger('id_compra')->nullable();
            $table->unsignedBigInteger('id_entrada')->nullable();
            $table->unsignedBigInteger('id_devolucion')->nullable();
            $table->unsignedBigInteger('id_traslado')->nullable();
            $table->unsignedBigInteger('id_salida')->nullable();

            $table->foreign('id_tipo')->references('id')->on('tipo_transacion')->onDelete('set null');
            $table->foreign('id_compra')->references('id')->on('compra')->onDelete('set null');
            $table->foreign('id_entrada')->references('id')->on('entrada')->onDelete('set null');
            $table->foreign('id_devolucion')->references('id')->on('devolucion')->onDelete('set null');
            $table->foreign('id_traslado')->references('id')->on('traslado')->onDelete('set null');
            $table->foreign('id_salida')->references('id')->on('salida')->onDelete('set null');
        });

        // Tabla de lotes
        Schema::create('lote', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad')->default(0);
            $table->integer('cantidad_inicial')->default(0);
            $table->datetime('fecha_ingreso');
            $table->double('precio_ingreso')->default(0);
            $table->text('observaciones')->nullable();
            $table->string('id_producto');
            $table->unsignedBigInteger('id_bodega')->nullable();
            $table->boolean('estado')->default(true);
            $table->unsignedBigInteger('id_transaccion')->nullable();

            $table->foreign('id_producto')->references('id')->on('producto')->onDelete('cascade');
            $table->foreign('id_bodega')->references('id')->on('bodega')->onDelete('set null');
            $table->foreign('id_transaccion')->references('id')->on('transaccion')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lote');
        Schema::dropIfExists('transaccion');
    }
};
