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
        Schema::create('chi_tiet_phieu_nhap', function (Blueprint $table) {
            $table->id();
            $table->integer('so_luong');
            $table->decimal('gia_nhap', 10, 2);
            $table->decimal('thanh_tien', 15, 2);
            $table->date('ngay_nhap')->nullable();
            $table->foreignId('ma_phieu_nhap')->constrained('phieu_nhap');
            $table->foreignId('ma_hoa')->constrained('hoa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_phieu_nhap');
    }
};
