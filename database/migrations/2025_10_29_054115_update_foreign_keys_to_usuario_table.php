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
        // Actualizar foreign keys de las tablas de transacciones para apuntar a 'usuario' en lugar de 'users'

        // Tabla compra
        Schema::table('compra', function (Blueprint $table) {
            $table->dropForeign(['id_usuario']);
            $table->foreign('id_usuario')->references('id')->on('usuario')->onDelete('set null');
        });

        // Tabla entrada
        Schema::table('entrada', function (Blueprint $table) {
            $table->dropForeign(['id_usuario']);
            $table->foreign('id_usuario')->references('id')->on('usuario')->onDelete('set null');
        });

        // Tabla traslado
        Schema::table('traslado', function (Blueprint $table) {
            $table->dropForeign(['id_usuario']);
            $table->foreign('id_usuario')->references('id')->on('usuario')->onDelete('set null');
        });

        // Tabla devolucion
        Schema::table('devolucion', function (Blueprint $table) {
            $table->dropForeign(['id_usuario']);
            $table->foreign('id_usuario')->references('id')->on('usuario')->onDelete('set null');
        });

        // Tabla salida
        Schema::table('salida', function (Blueprint $table) {
            $table->dropForeign(['id_usuario']);
            $table->foreign('id_usuario')->references('id')->on('usuario')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a foreign keys hacia 'users'

        Schema::table('compra', function (Blueprint $table) {
            $table->dropForeign(['id_usuario']);
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('entrada', function (Blueprint $table) {
            $table->dropForeign(['id_usuario']);
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('traslado', function (Blueprint $table) {
            $table->dropForeign(['id_usuario']);
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('devolucion', function (Blueprint $table) {
            $table->dropForeign(['id_usuario']);
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('salida', function (Blueprint $table) {
            $table->dropForeign(['id_usuario']);
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('set null');
        });
    }
};
