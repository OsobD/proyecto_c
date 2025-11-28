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
        Schema::create('solicitud_aprobacion', function (Blueprint $table) {
            $table->id();
            $table->string('tipo'); // 'EDICION', 'ELIMINACION'
            $table->string('tabla'); // 'compra'
            $table->unsignedBigInteger('registro_id');
            $table->json('datos')->nullable(); // Datos nuevos para edición
            $table->unsignedBigInteger('solicitante_id');
            $table->string('estado')->default('PENDIENTE'); // PENDIENTE, APROBADO, RECHAZADO
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('solicitante_id')->references('id')->on('usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_aprobacion');
    }
};
