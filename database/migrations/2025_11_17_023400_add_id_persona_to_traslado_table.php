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
        Schema::table('traslado', function (Blueprint $table) {
            $table->unsignedBigInteger('id_persona')->nullable()->after('id_usuario');

            $table->foreign('id_persona')->references('id')->on('persona')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('traslado', function (Blueprint $table) {
            $table->dropForeign(['id_persona']);
            $table->dropColumn('id_persona');
        });
    }
};
