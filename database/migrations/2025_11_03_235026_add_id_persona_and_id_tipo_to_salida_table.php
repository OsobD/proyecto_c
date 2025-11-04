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
        Schema::table('salida', function (Blueprint $table) {
            // Agregar campo id_persona
            $table->unsignedBigInteger('id_persona')->nullable()->after('id_bodega');
            $table->foreign('id_persona')->references('id')->on('persona')->onDelete('set null');

            // Agregar campo id_tipo
            $table->unsignedBigInteger('id_tipo')->nullable()->after('id_persona');
            $table->foreign('id_tipo')->references('id')->on('tipo_salida')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salida', function (Blueprint $table) {
            $table->dropForeign(['id_persona']);
            $table->dropColumn('id_persona');

            $table->dropForeign(['id_tipo']);
            $table->dropColumn('id_tipo');
        });
    }
};
