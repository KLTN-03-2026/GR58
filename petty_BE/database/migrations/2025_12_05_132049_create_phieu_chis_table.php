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
        Schema::create('phieu_chis', function (Blueprint $table) {
            $table->id();
            $table->string('ma_phieu_chi', 50)->unique();

            // Loại phiếu chi: chi_nhap_hang hoặc chi_van_hanh
            $table->enum('loai_phieu_chi', ['chi_nhap_hang', 'chi_van_hanh'])->comment('Loại phiếu chi');

            // Trường chung cho cả 2 loại
            $table->text('ly_do_chi')->comment('Lý do chi');
            $table->decimal('tong_so_tien', 15, 2)->comment('Tổng số tiền chi');
            $table->decimal('so_tien_thanh_toan_ngay', 15, 2)->default(0)->comment('Số tiền thanh toán ngay');
            $table->decimal('so_tien_con_no', 15, 2)->default(0)->comment('Số tiền còn nợ = Tổng - Thanh toán ngay');

            // Nguồn tiền
            $table->decimal('tien_mat', 15, 2)->default(0)->comment('Số tiền mặt');
            $table->decimal('tien_chuyen_khoan', 15, 2)->default(0)->comment('Số tiền chuyển khoản');

            // Ảnh chứng từ
            $table->json('anh_chung_tu')->nullable()->comment('Danh sách ảnh chứng từ (JSON array)');

            // Đối tượng nhận tiền (dành cho chi phí vận hành)
            $table->string('doi_tuong_nhan_tien')->nullable()->comment('Đối tượng nhận tiền (chi vận hành)');

            // Kết nối với nhà cung cấp (dành cho chi nhập hàng)
            $table->foreignId('nha_cung_cap_id')->nullable()->constrained('nha_cung_caps')->onDelete('set null')->comment('Nhà cung cấp (chi nhập hàng)');

            // Trạng thái
            $table->enum('trang_thai', ['con_no', 'da_hoan_thanh'])->default('con_no')->comment('Trạng thái: con_no (còn nợ NCC) hoặc da_hoan_thanh');

            // Người tạo phiếu (admin hoặc nhân viên đăng nhập)
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null');
            $table->foreignId('nhan_vien_id')->nullable()->constrained('nhan_viens')->onDelete('set null');
            $table->string('nguoi_tao_type')->nullable()->comment('Admin hoặc NhanVien');

            $table->date('ngay_chi')->comment('Ngày chi');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_chis');
    }
};
