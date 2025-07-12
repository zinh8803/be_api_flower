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
        Schema::create('hoa', function (Blueprint $table) {
            $table->id();
            $table->string('ten_hoa');
            $table->decimal('gia', 10, 2);
            $table->string('mau_sac')->nullable();
            $table->foreignId('ma_loai_hoa')->constrained('loai_hoa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoa');
    }
};
