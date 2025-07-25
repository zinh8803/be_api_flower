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
        Schema::create('product_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('order_detail_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->integer('quantity')->default(1);
            $table->string('reason')->nullable();
            $table->text('admin_note')->nullable();
            $table->string('image_url')->nullable();
            $table->enum('status', ['Đang xử lý', 'Từ chối', 'Đã giải quyết'])->default('Đang xử lý');
            $table->enum('action', ['Đổi hàng', 'Mã giảm giá'])->default('Đổi hàng');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reports');
    }
};
