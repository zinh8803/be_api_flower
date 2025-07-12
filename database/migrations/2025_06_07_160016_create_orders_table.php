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
        Schema::create('don_hang', function (Blueprint $table) {
            $table->id();
            $table->string('ten_nguoi_mua');
            $table->string('email');
            $table->string('so_dien_thoai');
            $table->string('dia_chi');
            $table->string('ghi_chu')->nullable();
            $table->decimal('tong_tien', 15, 2);
            $table->string('trang_thai');
            $table->decimal('so_tien_giam_gia', 10, 2)->default(0);
            $table->dateTime('mua_luc')->useCurrent();
            $table->string('phuong_thuc_thanh_toan')->default('cod');
            $table->foreignId('ma_giam_gia')->nullable()->constrained();
            $table->foreignId('ma_nguoi_dung')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('don_hang');
    }
};
