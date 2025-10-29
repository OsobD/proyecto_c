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
        Schema::create('tarjeta_responsabilidad', function (Blueprint $table) {
            $table->id();
            $table->datetime('fecha_creacion');
            $table->double('total')->default(0);
            $table->unsignedBigInteger('id_persona')->nullable();

            $table->foreign('id_persona')->references('id')->on('persona')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarjeta_responsabilidad');
    }
};
