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
        Schema::table('chi_tiet_don_hang', function (Blueprint $table) {
            $table->unsignedBigInteger('ma_size')->after('ma_san_pham');
            $table->foreign('ma_size')->references('id')->on('size_san_pham')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chi_tiet_don_hang', function (Blueprint $table) {
            $table->dropForeign(['ma_size']);
            $table->dropColumn('ma_size');
        });
    }
};
