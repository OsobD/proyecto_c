<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rol_permiso', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_rol');
            $table->unsignedBigInteger('id_permiso');
            $table->timestamps();

            // Llaves foráneas
            $table->foreign('id_rol')
                  ->references('id')
                  ->on('rol')
                  ->onDelete('cascade');
            
            $table->foreign('id_permiso')
                  ->references('id')
                  ->on('permiso')
                  ->onDelete('cascade');
            
            // Evitar duplicados
            $table->unique(['id_rol', 'id_permiso']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rol_permiso');
    }
};