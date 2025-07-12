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
            $table->string('ten_giam_gia')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ma_giam_gia', function (Blueprint $table) {
            $table->dropColumn('ten_giam_gia');
        });
    }
};
