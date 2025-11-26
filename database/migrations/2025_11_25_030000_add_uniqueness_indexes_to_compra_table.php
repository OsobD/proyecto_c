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
        Schema::table('compra', function (Blueprint $table) {
            // Index for global uniqueness check of correlativo
            $table->index('correlativo');
            
            // Composite index for scoped uniqueness check of invoice number per provider
            $table->index(['id_proveedor', 'no_factura', 'no_serie'], 'idx_compra_proveedor_factura');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compra', function (Blueprint $table) {
            $table->dropIndex(['correlativo']);
            $table->dropIndex('idx_compra_proveedor_factura');
        });
    }
};
