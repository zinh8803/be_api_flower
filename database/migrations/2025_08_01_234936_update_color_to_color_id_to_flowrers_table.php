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
        Schema::table('flowers', function (Blueprint $table) {
            $table->unsignedBigInteger('color_id')->nullable()->after('price');
            $table->foreign('color_id')->references('id')->on('colors');
            $table->dropColumn('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flowers', function (Blueprint $table) {
            $table->string('color')->nullable();
            $table->dropForeign(['color_id']);
            $table->dropColumn('color_id');
        });
    }
};
