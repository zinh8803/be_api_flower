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
        Schema::create('phieu_nhap', function (Blueprint $table) {
            $table->id();
            $table->string('ghi_chu')->nullable();
            $table->date('ngay_nhap');
            $table->decimal('tong_tien', 15, 2);
            $table->foreignId('ma_nguoi_dung')->nullable()->constrained('nguoi_dung');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_nhap');
    }
};
