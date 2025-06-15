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
        Schema::table('import_receipt_details', function (Blueprint $table) {
            $table->integer('used_quantity')->default(0)->after('quantity');
            $table->integer('remaining_quantity')->default(0)->after('used_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('import_receipt_details', function (Blueprint $table) {
            $table->dropColumn('used_quantity');
            $table->dropColumn('remaining_quantity');
        });
    }
};
