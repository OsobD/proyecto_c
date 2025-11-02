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
            $table->unsignedBigInteger('id_tipo_devolucion')->nullable()->after('id_bodega');
            $table->unsignedBigInteger('id_razon_devolucion')->nullable()->after('id_tipo_devolucion');

            $table->foreign('id_tipo_devolucion')->references('id')->on('tipo_devolucion')->onDelete('set null');
            $table->foreign('id_razon_devolucion')->references('id')->on('razon_devolucion')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devolucion', function (Blueprint $table) {
            $table->dropForeign(['id_tipo_devolucion']);
            $table->dropForeign(['id_razon_devolucion']);
            $table->dropColumn(['id_tipo_devolucion', 'id_razon_devolucion']);
        });
    }
};
