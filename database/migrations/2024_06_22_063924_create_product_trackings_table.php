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
        Schema::create('product_trackings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('product_code')->nullable();
            $table->string('product_name')->nullable();
            $table->string('from');
            $table->text('from_details')->nullable();
            $table->string('to');
            $table->text('to_details')->nullable();
            $table->integer('quantity');
            $table->decimal('price', 8, 2);
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_trackings');
    }
};
