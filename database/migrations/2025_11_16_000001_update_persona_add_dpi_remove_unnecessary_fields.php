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
        Schema::table('persona', function (Blueprint $table) {
            // Agregar campo DPI
            $table->string('dpi', 13)->nullable()->after('apellidos');

            // Eliminar campos innecesarios
            $table->dropColumn(['fecha_nacimiento', 'genero']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persona', function (Blueprint $table) {
            // Restaurar campos eliminados
            $table->date('fecha_nacimiento')->nullable();
            $table->string('genero', 1)->nullable();

            // Eliminar campo DPI
            $table->dropColumn('dpi');
        });
    }
};
