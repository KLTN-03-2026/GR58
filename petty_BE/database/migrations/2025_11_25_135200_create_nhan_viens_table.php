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
        Schema::create('nhan_viens', function (Blueprint $table) {
            $table->id();
            // Thông tin cơ bản
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('anh_dai_dien')->nullable();

            // Vai trò: bác sĩ hoặc y tá
            $table->enum('vai_tro', ['bac_si', 'y_ta'])->default('bac_si');

            // Chức danh, số năm kinh nghiệm
            $table->string('chuc_danh')->nullable();
            $table->unsignedInteger('nam_kinh_nghiem')->default(0);

            // Chứng chỉ hành nghề và bằng cấp chuyên môn
            $table->text('chung_chi_hanh_nghe')->nullable();
            $table->text('bang_cap_chuyen_mon')->nullable();

            // Mật khẩu
            $table->string('password');

            // Trạng thái: hoạt động hoặc đã khóa
            $table->enum('trang_thai', ['hoat_dong', 'da_khoa'])->default('hoat_dong');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nhan_viens');
    }
};
