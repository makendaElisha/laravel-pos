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
        Schema::table('stock_mouvements', function (Blueprint $table) {
            $table->decimal('quantity_before')->nullable();
            $table->decimal('quantity_after')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_mouvements', function (Blueprint $table) {
            $table->dropColumn(['quantity_before']);
            $table->dropColumn(['quantity_after']);
        });
    }
};
