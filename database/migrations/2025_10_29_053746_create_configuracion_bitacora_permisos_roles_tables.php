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
        // Tabla de configuraciones
        Schema::create('configuracion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
        });

        // Tabla de bitácora
        Schema::create('bitacora', function (Blueprint $table) {
            $table->id();
        });

        // Tabla de permisos
        Schema::create('permiso', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->unsignedBigInteger('id_configuracion')->nullable();
            $table->unsignedBigInteger('id_bitacora')->nullable();

            $table->foreign('id_configuracion')->references('id')->on('configuracion')->onDelete('set null');
            $table->foreign('id_bitacora')->references('id')->on('bitacora')->onDelete('set null');
        });

        // Tabla de roles
        Schema::create('rol', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->unsignedBigInteger('id_permiso')->nullable();

            $table->foreign('id_permiso')->references('id')->on('permiso')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rol');
        Schema::dropIfExists('permiso');
        Schema::dropIfExists('bitacora');
        Schema::dropIfExists('configuracion');
    }
};
