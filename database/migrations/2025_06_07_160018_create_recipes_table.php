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
        Schema::create('cong_thuc', function (Blueprint $table) {
            $table->id();
            $table->integer('so_luong');
            $table->foreignId('ma_san_pham')->constrained('san_pham');
            $table->foreignId('ma_hoa')->constrained('hoa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cong_thuc');
    }
};
