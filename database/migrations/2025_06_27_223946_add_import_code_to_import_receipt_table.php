<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('import_receipts', function (Blueprint $table) {
            $table->string('import_code')->unique()->nullable()->after('id');
        });

        $receipts = DB::table('import_receipts')->whereNull('import_code')->get();
        foreach ($receipts as $receipt) {
            do {
                $importCode = 'PN' . date('YmdHis') . rand(100, 999);
            } while (DB::table('import_receipts')->where('import_code', $importCode)->exists());

            DB::table('import_receipts')
                ->where('id', $receipt->id)
                ->update(['import_code' => $importCode]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('import_receipts', function (Blueprint $table) {
            $table->dropColumn('import_code');
        });
    }
};
