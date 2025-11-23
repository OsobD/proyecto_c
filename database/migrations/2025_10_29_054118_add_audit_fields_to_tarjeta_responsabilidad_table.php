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
        Schema::table('tarjeta_responsabilidad', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('id_persona');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            $table->timestamps(); // Agrega created_at y updated_at

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('usuario')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('usuario')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tarjeta_responsabilidad', function (Blueprint $table) {
            // Eliminar foreign keys primero
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Eliminar columnas
            $table->dropColumn(['created_by', 'updated_by', 'created_at', 'updated_at']);
        });
    }
};
