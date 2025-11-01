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
        Schema::create('usuario', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_usuario')->unique();
            $table->string('contrasena');
            $table->unsignedBigInteger('id_persona')->nullable();
            $table->unsignedBigInteger('id_rol')->nullable();
            $table->boolean('estado')->default(true);

            // Foreign keys
            $table->foreign('id_persona')->references('id')->on('persona')->onDelete('cascade');
            $table->foreign('id_rol')->references('id')->on('rol')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
