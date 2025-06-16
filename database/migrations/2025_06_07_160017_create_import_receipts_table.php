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
        Schema::create('import_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('note')->nullable();
            $table->date('import_date');
            $table->decimal('total_price', 12, 2);
            $table->foreignId('user_id')->nullable()->constrained('users');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_receipts');
    }
};
