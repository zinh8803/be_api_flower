<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chi_tiet_phieu_nhap', function (Blueprint $table) {
            $table->integer('so_luong_da_su_dung')->default(0)->after('so_luong');
            $table->integer('so_luong_con_lai')->default(0)->after('so_luong_da_su_dung');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chi_tiet_phieu_nhap', function (Blueprint $table) {
            $table->dropColumn('so_luong_da_su_dung');
            $table->dropColumn('so_luong_con_lai');
        });
    }
};
