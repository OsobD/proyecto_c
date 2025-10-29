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
        // Detalle de compras
        Schema::create('detalle_compra', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_compra')->nullable();
            $table->string('id_producto');
            $table->double('precio_ingreso')->default(0);
            $table->integer('cantidad')->default(0);

            $table->foreign('id_compra')->references('id')->on('compra')->onDelete('cascade');
            $table->foreign('id_producto')->references('id')->on('producto')->onDelete('cascade');
        });

        // Detalle de entradas
        Schema::create('detalle_entrada', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_entrada')->nullable();
            $table->string('id_producto');
            $table->integer('cantidad')->default(0);
            $table->decimal('precio_ingreso', 10, 2)->default(0);

            $table->foreign('id_entrada')->references('id')->on('entrada')->onDelete('cascade');
            $table->foreign('id_producto')->references('id')->on('producto')->onDelete('cascade');
        });

        // Detalle de devoluciones
        Schema::create('detalle_devolucion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_devolucion')->nullable();
            $table->string('id_producto');
            $table->unsignedBigInteger('id_lote')->nullable();
            $table->integer('cantidad')->default(0);

            $table->foreign('id_devolucion')->references('id')->on('devolucion')->onDelete('cascade');
            $table->foreign('id_producto')->references('id')->on('producto')->onDelete('cascade');
            $table->foreign('id_lote')->references('id')->on('lote')->onDelete('set null');
        });

        // Detalle de traslados
        Schema::create('detalle_traslado', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_traslado')->nullable();
            $table->string('id_producto');
            $table->integer('cantidad')->default(0);
            $table->unsignedBigInteger('id_lote')->nullable();
            $table->double('precio_traslado')->default(0);

            $table->foreign('id_traslado')->references('id')->on('traslado')->onDelete('cascade');
            $table->foreign('id_producto')->references('id')->on('producto')->onDelete('cascade');
            $table->foreign('id_lote')->references('id')->on('lote')->onDelete('set null');
        });

        // Detalle de salidas
        Schema::create('detalle_salida', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_salida')->nullable();
            $table->string('id_producto');
            $table->unsignedBigInteger('id_lote')->nullable();
            $table->integer('cantidad')->default(0);
            $table->decimal('precio_salida', 10, 2)->default(0);

            $table->foreign('id_salida')->references('id')->on('tipo_salida')->onDelete('cascade');
            $table->foreign('id_producto')->references('id')->on('producto')->onDelete('cascade');
            $table->foreign('id_lote')->references('id')->on('lote')->onDelete('set null');
        });

        // Tarjeta de producto
        Schema::create('tarjeta_producto', function (Blueprint $table) {
            $table->id();
            $table->double('precio_asignacion')->default(0);
            $table->unsignedBigInteger('id_tarjeta')->nullable();
            $table->string('id_producto');
            $table->unsignedBigInteger('id_lote')->nullable();

            $table->foreign('id_tarjeta')->references('id')->on('tarjeta_responsabilidad')->onDelete('cascade');
            $table->foreign('id_producto')->references('id')->on('producto')->onDelete('cascade');
            $table->foreign('id_lote')->references('id')->on('lote')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarjeta_producto');
        Schema::dropIfExists('detalle_salida');
        Schema::dropIfExists('detalle_traslado');
        Schema::dropIfExists('detalle_devolucion');
        Schema::dropIfExists('detalle_entrada');
        Schema::dropIfExists('detalle_compra');
    }
};
