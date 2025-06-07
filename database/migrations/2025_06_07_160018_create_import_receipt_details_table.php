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
        Schema::create('import_receipt_details', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->decimal('import_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->foreignId('import_receipt_id')->constrained();
            $table->foreignId('flower_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_receipt_details');
    }
};
