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
        Schema::create('auto_import_receipts', function (Blueprint $table) {
            $table->id();
            $table->date('import_date');
            $table->json('details');
            $table->boolean('enabled')->default(true);
            $table->time('run_time')->default('11:50:00');
            $table->unsignedBigInteger('auto_import_receipt_id')->nullable();
            $table->foreign('auto_import_receipt_id')->references('id')->on('import_receipts')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_import_receipts');
    }
};
