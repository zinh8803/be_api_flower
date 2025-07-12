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
        Schema::table('ma_giam_gia', function (Blueprint $table) {
            $table->boolean('trang_thai')->default(true)->after('ngay_ket_thuc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ma_giam_gia', function (Blueprint $table) {
            $table->dropColumn('trang_thai');
        });
    }
};
