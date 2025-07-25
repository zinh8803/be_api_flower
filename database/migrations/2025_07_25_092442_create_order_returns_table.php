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
        Schema::create('order_returns', function (Blueprint $table) {
            $table->id();
            $table->string('order_return_code')->unique()->nullable();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('order_detail_id');
            $table->integer('quantity_returned');
            $table->unsignedBigInteger('user_id');
            $table->string('status')->default('Đang xử lý');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('order_detail_id')->references('id')->on('order_details')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_returns');
    }
};
