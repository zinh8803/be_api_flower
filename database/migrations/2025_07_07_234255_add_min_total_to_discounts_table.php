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
            $table->unsignedInteger('giam_gia_toi_thieu')->default(0)->after('value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ma_giam_gia', function (Blueprint $table) {
            $table->dropColumn('giam_gia_toi_thieu');
        });
    }
};
