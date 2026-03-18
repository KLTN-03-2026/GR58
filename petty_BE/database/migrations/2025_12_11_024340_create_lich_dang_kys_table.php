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
        Schema::create('lich_dang_kys', function (Blueprint $table) {
            $table->id();

            // Ngày giờ đăng ký
            $table->dateTime('ngay_gio')->comment('Ngày giờ đăng ký khám');

            // Ghi chú
            $table->text('ghi_chu')->nullable()->comment('Ghi chú của khách hàng');

            // Trạng thái
            $table->string('trang_thai', 50)->default('chua_xac_nhan')->comment('Trạng thái: chua_xac_nhan, da_xac_nhan, tu_choi');

            // Quan hệ
            $table->foreignId('nhan_vien_id')->nullable()->constrained('nhan_viens')->nullOnDelete()->comment('Nhân viên phụ trách');
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete()->comment('Admin xác nhận');
            $table->foreignId('lich_lam_viec_id')->nullable()->constrained('lich_lam_viecs')->nullOnDelete()->comment('Lịch làm việc tương ứng');
            $table->foreignId('khach_hang_id')->constrained('khach_hangs')->cascadeOnDelete();
            $table->foreignId('thu_cung_id')->nullable()->constrained('thu_cungs')->nullOnDelete();
            $table->foreignId('dich_vu_id')->nullable()->constrained('dich_vus')->nullOnDelete();

            $table->timestamps();

            // Index
            $table->index('ngay_gio');
            $table->index('trang_thai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_dang_kys');
    }
};
