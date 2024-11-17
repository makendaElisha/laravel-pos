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
        Schema::table('updated_stocks', function (Blueprint $table) {
            $table->boolean("is_from_petit_depot")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('updated_stocks', function (Blueprint $table) {
            $table->dropColumn(['is_from_petit_depot']);
        });
    }
};
