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
            // Agregar correlativo y no_serie después de no_formulario
            $table->string('correlativo')->nullable()->after('no_formulario');
            $table->string('no_serie')->nullable()->after('correlativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devolucion', function (Blueprint $table) {
            $table->dropColumn(['correlativo', 'no_serie']);
        });
    }
};
