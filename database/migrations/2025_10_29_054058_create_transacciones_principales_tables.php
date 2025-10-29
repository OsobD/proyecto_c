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
        // Tabla de compras
        Schema::create('compra', function (Blueprint $table) {
            $table->id();
            $table->datetime('fecha');
            $table->string('no_serie')->nullable();
            $table->string('no_factura')->nullable();
            $table->string('correltivo')->nullable();
            $table->double('total')->default(0);
            $table->unsignedBigInteger('id_proveedor')->nullable();
            $table->unsignedBigInteger('id_bodega')->nullable();
            $table->unsignedBigInteger('id_usuario')->nullable();

            $table->foreign('id_proveedor')->references('id')->on('proveedor')->onDelete('set null');
            $table->foreign('id_bodega')->references('id')->on('bodega')->onDelete('set null');
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('set null');
        });

        // Tabla de entradas
        Schema::create('entrada', function (Blueprint $table) {
            $table->id();
            $table->datetime('fecha');
            $table->double('total')->default(0);
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->unsignedBigInteger('id_tarjeta')->nullable();
            $table->unsignedBigInteger('id_bodega')->nullable();

            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_tarjeta')->references('id')->on('tarjeta_responsabilidad')->onDelete('set null');
            $table->foreign('id_bodega')->references('id')->on('bodega')->onDelete('set null');
        });

        // Tabla de traslados
        Schema::create('traslado', function (Blueprint $table) {
            $table->id();
            $table->datetime('fecha');
            $table->string('no_requisicion')->nullable();
            $table->double('total')->default(0);
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->unsignedBigInteger('id_bodega')->nullable();
            $table->unsignedBigInteger('id_tarjeta')->nullable();

            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_bodega')->references('id')->on('bodega')->onDelete('set null');
        });

        // Tabla de devoluciones (depende de traslado)
        Schema::create('devolucion', function (Blueprint $table) {
            $table->id();
            $table->datetime('fecha');
            $table->string('no_formulario')->nullable();
            $table->string('foto')->nullable();
            $table->double('total')->default(0);
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->unsignedBigInteger('id_tarjeta')->nullable();
            $table->unsignedBigInteger('id_bodega')->nullable();
            $table->unsignedBigInteger('id_traslado')->nullable();

            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_bodega')->references('id')->on('bodega')->onDelete('set null');
            $table->foreign('id_traslado')->references('id')->on('traslado')->onDelete('set null');
        });

        // Tabla de salidas
        Schema::create('salida', function (Blueprint $table) {
            $table->id();
            $table->datetime('fecha');
            $table->double('total')->default(0);
            $table->text('descripcion')->nullable();
            $table->string('ubicacion')->nullable();
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->unsignedBigInteger('id_tarjeta')->nullable();
            $table->unsignedBigInteger('id_bodega')->nullable();

            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_bodega')->references('id')->on('bodega')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salida');
        Schema::dropIfExists('devolucion');
        Schema::dropIfExists('traslado');
        Schema::dropIfExists('entrada');
        Schema::dropIfExists('compra');
    }
};
