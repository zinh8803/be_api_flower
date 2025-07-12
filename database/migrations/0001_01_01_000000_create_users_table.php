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
        Schema::create('nguoi_dung', function (Blueprint $table) {
            $table->id();
            $table->string('ten');
            $table->string('email')->unique();
            $table->timestamp('email_xac_thuc')->nullable();
            $table->string('mat_khau');
            $table->string('hinh_anh')->default('https://res.cloudinary.com/def4sm0df/image/upload/v1750084996/avatars/hvoc0rxofqwfaudmooil.jpg');
            $table->string('dia_chi')->nullable();
            $table->string('so_dien_thoai')->nullable();
            $table->boolean('trang_thai')->default(1);
            $table->enum('vai_tro', ['admin', 'user', 'employee'])->default('user');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('mat_khau_reset', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('ngay_tao')->nullable();
        });

        Schema::create('phien_lam_viec', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('nguoi_dung_id')->nullable()->index();
            $table->string('dia_chi_ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('hoat_dong_cuoi')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nguoi_dung');
        Schema::dropIfExists('mat_khau_reset');
        Schema::dropIfExists('phien_lam_viec');
    }
};
