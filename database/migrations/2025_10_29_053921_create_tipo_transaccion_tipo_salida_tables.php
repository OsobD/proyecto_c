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
        // Tabla de tipos de transacción (nota: nombre con typo en el modelo)
        Schema::create('tipo_transacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
        });

        // Tabla de tipos de salida
        Schema::create('tipo_salida', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->unsignedBigInteger('id_salida')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_salida');
        Schema::dropIfExists('tipo_transacion');
    }
};
