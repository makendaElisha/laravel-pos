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
        Schema::create('updated_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->nullable();
            $table->foreignId('product_id')->nullable();
            $table->decimal('quantity')->nullable();
            $table->foreignId('sent_by')->nullable();
            $table->foreignId('seen_by')->nullable();

            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('sent_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('seen_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('updated_stocks');
    }
};
