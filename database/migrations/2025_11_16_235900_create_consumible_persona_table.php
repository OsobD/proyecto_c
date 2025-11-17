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
        Schema::create('consumible_persona', function (Blueprint $table) {
            $table->id();
            $table->string('correlativo'); // Para agrupar por requisición (REQ-001)
            $table->date('fecha');
            $table->unsignedBigInteger('id_persona');
            $table->unsignedBigInteger('id_producto');
            $table->unsignedBigInteger('id_lote')->nullable();
            $table->integer('cantidad');
            $table->double('precio_unitario');
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('id_bodega'); // De qué bodega salió
            $table->timestamps();

            $table->foreign('id_persona')->references('id')->on('persona')->onDelete('cascade');
            $table->foreign('id_producto')->references('id')->on('producto')->onDelete('cascade');
            $table->foreign('id_lote')->references('id')->on('lote')->onDelete('set null');
            $table->foreign('id_bodega')->references('id')->on('bodega')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumible_persona');
    }
};
