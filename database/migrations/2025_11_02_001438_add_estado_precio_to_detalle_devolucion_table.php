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
        Schema::table('detalle_devolucion', function (Blueprint $table) {
            $table->enum('estado_producto', ['bueno', 'regular', 'malo'])->default('bueno')->after('cantidad');
            $table->decimal('precio_unitario', 10, 2)->nullable()->after('estado_producto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_devolucion', function (Blueprint $table) {
            $table->dropColumn(['estado_producto', 'precio_unitario']);
        });
    }
};
