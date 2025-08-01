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
        Schema::table('auto_import_receipts', function (Blueprint $table) {
            $table->boolean('repeat_daily')->default(false)->after('enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auto_import_receipts', function (Blueprint $table) {
            $table->dropColumn('repeat_daily');
        });
    }
};
