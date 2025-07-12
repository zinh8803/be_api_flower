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
        Schema::create('size_san_pham', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ma_san_pham')->constrained()->onDelete('cascade');
            $table->string('kich_thuoc');
            $table->decimal('gia', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('size_san_pham');
    }
};
