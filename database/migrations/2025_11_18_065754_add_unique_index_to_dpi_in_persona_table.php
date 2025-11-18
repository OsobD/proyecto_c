<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Agrega un índice único al campo DPI para garantizar que no existan
     * dos personas con el mismo número de documento de identificación.
     */
    public function up(): void
    {
        Schema::table('persona', function (Blueprint $table) {
            // Agregar índice único al DPI
            $table->unique('dpi', 'persona_dpi_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persona', function (Blueprint $table) {
            // Eliminar el índice único
            $table->dropUnique('persona_dpi_unique');
        });
    }
};
