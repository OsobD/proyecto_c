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
        Schema::table('traslado', function (Blueprint $table) {
            // Renombrar id_bodega a id_bodega_origen
            $table->renameColumn('id_bodega', 'id_bodega_origen');

            // Agregar bodega destino
            $table->unsignedBigInteger('id_bodega_destino')->nullable()->after('id_bodega_origen');
            $table->foreign('id_bodega_destino')->references('id')->on('bodega')->onDelete('set null');

            // Agregar campos adicionales
            $table->string('correlativo')->nullable()->after('no_requisicion');
            $table->enum('estado', ['Pendiente', 'En Tránsito', 'Completado', 'Cancelado'])->default('Pendiente')->after('correlativo');
            $table->text('observaciones')->nullable()->after('descripcion');
            $table->boolean('activo')->default(true)->after('id_tarjeta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('traslado', function (Blueprint $table) {
            // Eliminar foreign key y columna de bodega destino
            $table->dropForeign(['id_bodega_destino']);
            $table->dropColumn('id_bodega_destino');

            // Eliminar campos adicionales
            $table->dropColumn(['correlativo', 'estado', 'observaciones', 'activo']);

            // Renombrar id_bodega_origen de vuelta a id_bodega
            $table->renameColumn('id_bodega_origen', 'id_bodega');
        });
    }
};
