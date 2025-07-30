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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivery_time');
            $table->enum('delivery_time_slot', ['Buổi sáng', 'Buổi chiều'])
                ->nullable()
                ->after('delivery_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivery_time_slot');
            $table->time('delivery_time')->nullable()->after('delivery_date');
        });
    }
};
