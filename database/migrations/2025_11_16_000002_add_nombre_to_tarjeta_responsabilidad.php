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
            // Agregar campo nombre para identificar la tarjeta
            $table->string('nombre')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tarjeta_responsabilidad', function (Blueprint $table) {
            $table->dropColumn('nombre');
        });
    }
};
