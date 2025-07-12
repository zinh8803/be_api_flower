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
        Schema::table('phieu_nhap', function (Blueprint $table) {
            $table->string('ma_phieu_nhap')->unique()->nullable()->after('id');
        });

        $receipts = DB::table('phieu_nhap')->whereNull('ma_phieu_nhap')->get();
        foreach ($receipts as $receipt) {
            do {
                $importCode = 'PN' . date('YmdHis') . rand(100, 999);
            } while (DB::table('phieu_nhap')->where('ma_phieu_nhap', $importCode)->exists());

            DB::table('phieu_nhap')
                ->where('id', $receipt->id)
                ->update(['ma_phieu_nhap' => $importCode]);
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
