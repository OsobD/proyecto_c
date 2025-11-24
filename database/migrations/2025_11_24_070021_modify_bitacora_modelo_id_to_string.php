<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bitacora', function (Blueprint $table) {
            // Drop the composite index first
            $table->dropIndex(['modelo', 'modelo_id']);

            // Change modelo_id from unsignedBigInteger to string
            $table->string('modelo_id', 255)->nullable()->change();

            // Recreate the composite index
            $table->index(['modelo', 'modelo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bitacora', function (Blueprint $table) {
            // Drop the composite index first
            $table->dropIndex(['modelo', 'modelo_id']);

            // Change back to unsignedBigInteger
            $table->unsignedBigInteger('modelo_id')->nullable()->change();

            // Recreate the composite index
            $table->index(['modelo', 'modelo_id']);
        });
    }
};
