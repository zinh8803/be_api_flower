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
        Schema::create('tu_dong_nhap_kho', function (Blueprint $table) {
            $table->id();
            $table->date('ngay_nhap_kho')->default(now());
            $table->json('chi_tiet')->nullable();
            $table->boolean('kich_hoat')->default(true);
            $table->time('thoi_gian_chay')->default('11:50:00');
            $table->unsignedBigInteger('phieu_nhap_id')->nullable();
            $table->foreign('phieu_nhap_id')->references('id')->on('phieu_nhap')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tu_dong_nhap_kho');
    }
};
