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
        Schema::create('lich_su_thanh_toan_phieu_chis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phieu_chi_id')
                ->constrained('phieu_chis')
                ->onDelete('cascade')
                ->comment('ID phiếu chi');

            $table->decimal('so_tien_thanh_toan', 15, 2)
                ->comment('Số tiền thanh toán lần này');

            $table->enum('hinh_thuc_thanh_toan', ['tien_mat', 'chuyen_khoan', 'ca_hai'])
                ->default('tien_mat')
                ->comment('Hình thức thanh toán');

            $table->decimal('tien_mat', 15, 2)
                ->default(0)
                ->comment('Số tiền mặt');

            $table->decimal('tien_chuyen_khoan', 15, 2)
                ->default(0)
                ->comment('Số tiền chuyển khoản');

            $table->text('ghi_chu')
                ->nullable()
                ->comment('Ghi chú thanh toán');

            $table->decimal('so_tien_da_tra_truoc_do', 15, 2)
                ->comment('Tổng đã trả trước đó');

            $table->decimal('so_tien_con_no_sau_thanh_toan', 15, 2)
                ->comment('Còn nợ sau khi thanh toán lần này');

            $table->dateTime('ngay_thanh_toan')
                ->comment('Ngày giờ thanh toán');

            // Người thanh toán có thể là admin hoặc nhân viên
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null');
            $table->foreignId('nhan_vien_id')->nullable()->constrained('nhan_viens')->onDelete('set null');
            $table->string('nguoi_thanh_toan_type')->nullable()->comment('Admin hoặc NhanVien');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('phieu_chi_id');
            $table->index('ngay_thanh_toan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_su_thanh_toan_phieu_chis');
    }
};
