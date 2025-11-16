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
        Schema::table('entrada', function (Blueprint $table) {
            // Agregar correlativo y no_serie después de descripcion
            $table->string('correlativo')->nullable()->after('descripcion');
            $table->string('no_serie')->nullable()->after('correlativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entrada', function (Blueprint $table) {
            $table->dropColumn(['correlativo', 'no_serie']);
        });
    }
};
