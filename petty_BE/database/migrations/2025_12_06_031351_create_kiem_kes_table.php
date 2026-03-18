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
        Schema::create('kiem_kes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hang_hoa_id')->constrained('hang_hoas')->onDelete('cascade');
            $table->foreignId('chi_tiet_phieu_nhap_kho_id')->nullable()->constrained('chi_tiet_phieu_nhap_khos')->onDelete('set null');
            $table->integer('so_luong_he_thong')->default(0)->comment('Số lượng theo hệ thống');
            $table->integer('so_luong_thuc_te')->default(0)->comment('Số lượng thực tế kiểm');
            $table->integer('chenh_lech')->default(0)->comment('Chênh lệch = Thực tế - Hệ thống');
            $table->string('ly_do')->nullable()->comment('Lý do chênh lệch');
            $table->date('ngay_kiem_ke')->comment('Ngày kiểm kê');

            // Người kiểm kê có thể là Admin hoặc Nhân viên
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null');
            $table->foreignId('nhan_vien_id')->nullable()->constrained('nhan_viens')->onDelete('set null');
            $table->string('nguoi_kiem_ke_type')->nullable()->comment('Admin hoặc NhanVien');

            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kiem_kes');
    }
};
