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
        Schema::create('phieu_khams', function (Blueprint $table) {
            $table->id();

            // Thông số sức khỏe
            $table->decimal('nhiet_do', 5, 2)->nullable()->comment('Nhiệt độ (°C)');
            $table->decimal('can_nang', 8, 2)->nullable()->comment('Cân nặng (kg)');
            $table->integer('nhip_tim')->nullable()->comment('Nhịp tim (bpm)');
            $table->integer('nhip_tho')->nullable()->comment('Nhịp thở (lần/phút)');

            // Lý do khám
            $table->string('ly_do_den_kham', 255)->nullable()->comment('Lý do đến khám');

            // Triệu chứng và chẩn đoán
            $table->text('trieu_chung')->nullable()->comment('Triệu chứng');
            $table->text('chan_doan')->nullable()->comment('Chẩn đoán');

            // Ghi chú
            $table->text('ghi_chu')->nullable()->comment('Ghi chú thêm');

            // Loại chỉ định
            $table->string('loai_chi_dinh', 50)->nullable()->comment('Loại chỉ định (chi_dinh_can_lam_sang, don_thuoc, hen_tai_kham)');

            // Liên kết với lịch hẹn
            $table->foreignId('lich_hen_id')->nullable()->constrained('lich_hens')->nullOnDelete();

            // Liên kết với nhân viên
            $table->foreignId('nhan_vien_id')->nullable()->constrained('nhan_viens')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_khams');
    }
};
