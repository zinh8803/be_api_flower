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
        Schema::table('cong_thuc', function (Blueprint $table) {
            $table->foreignId('ma_size')->nullable()->after('so_luong')->constrained('size_san_pham')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cong_thuc', function (Blueprint $table) {
            $table->dropForeign(['ma_size']);
            $table->dropColumn('ma_size');
        });
    }
};
