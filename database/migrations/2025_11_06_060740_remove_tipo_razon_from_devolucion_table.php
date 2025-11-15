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
        // Eliminar foreign keys y columnas de la tabla devolucion
        if (Schema::hasColumn('devolucion', 'id_tipo_devolucion') || Schema::hasColumn('devolucion', 'id_razon_devolucion')) {
            Schema::table('devolucion', function (Blueprint $table) {
                // Eliminar foreign keys si existen (Laravel 11+ compatible)
                if (Schema::hasColumn('devolucion', 'id_tipo_devolucion')) {
                    try {
                        $table->dropForeign(['id_tipo_devolucion']);
                    } catch (\Exception $e) {
                        // Foreign key no existe, continuar
                    }
                    $table->dropColumn('id_tipo_devolucion');
                }

                if (Schema::hasColumn('devolucion', 'id_razon_devolucion')) {
                    try {
                        $table->dropForeign(['id_razon_devolucion']);
                    } catch (\Exception $e) {
                        // Foreign key no existe, continuar
                    }
                    $table->dropColumn('id_razon_devolucion');
                }
            });
        }

        // Eliminar columnas de detalle_devolucion si existen
        if (Schema::hasColumn('detalle_devolucion', 'estado_producto') || Schema::hasColumn('detalle_devolucion', 'precio_unitario')) {
            Schema::table('detalle_devolucion', function (Blueprint $table) {
                if (Schema::hasColumn('detalle_devolucion', 'estado_producto')) {
                    $table->dropColumn('estado_producto');
                }
                if (Schema::hasColumn('detalle_devolucion', 'precio_unitario')) {
                    $table->dropColumn('precio_unitario');
                }
            });
        }

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
