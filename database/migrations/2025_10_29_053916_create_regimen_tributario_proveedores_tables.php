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
        // Tabla de regímenes tributarios
        Schema::create('regimen_tributario', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
        });

        // Tabla de proveedores
        Schema::create('proveedor', function (Blueprint $table) {
            $table->id();
            $table->string('nit')->unique();
            $table->unsignedBigInteger('id_regimen')->nullable();
            $table->string('nombre');
            $table->boolean('estado')->default(true);

            $table->foreign('id_regimen')->references('id')->on('regimen_tributario')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedor');
        Schema::dropIfExists('regimen_tributario');
    }
};
