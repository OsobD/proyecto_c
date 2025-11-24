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
        Schema::table('rol', function (Blueprint $table) {
            // Eliminar foreign key antigua que no vamos a usar
            $table->dropForeign(['id_permiso']);
            $table->dropColumn('id_permiso');

            // Agregar descripción y timestamps
            $table->text('descripcion')->nullable()->after('nombre')->comment('Descripción del rol');
            $table->boolean('es_sistema')->default(false)->after('descripcion')->comment('Si es rol del sistema (no se puede eliminar)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rol', function (Blueprint $table) {
            // Eliminar columnas nuevas
            $table->dropColumn(['descripcion', 'es_sistema']);
            $table->dropTimestamps();

            // Restaurar columna antigua
            $table->unsignedBigInteger('id_permiso')->nullable();
            $table->foreign('id_permiso')->references('id')->on('permiso')->onDelete('set null');
        });
    }
};
