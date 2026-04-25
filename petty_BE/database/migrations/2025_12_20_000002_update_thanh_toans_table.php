<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('thanh_toans', function (Blueprint $table) {
            // Mã thanh toán duy nhất
            $table->string('ma_thanh_toan', 50)->unique()->after('id');

            // Liên kết lịch hẹn và khách hàng
            $table->foreignId('lich_hen_id')->nullable()->constrained('lich_hens')->nullOnDelete()->after('ma_thanh_toan');
            $table->foreignId('khach_hang_id')->nullable()->constrained('khach_hangs')->nullOnDelete()->after('lich_hen_id');

            // Tiền
            $table->decimal('tong_tien_goc', 15, 2)->default(0)->comment('Tổng trước giảm giá');
            $table->decimal('so_tien_giam', 15, 2)->default(0)->comment('Số tiền được giảm');
            $table->decimal('tong_tien_sau_giam', 15, 2)->default(0)->comment('Số tiền thực tế thanh toán');

            // Hình thức thanh toán
            $table->enum('hinh_thuc_thanh_toan', ['tien_mat', 'vnpay', 'momo', 'ket_hop'])
                ->default('tien_mat');
            $table->decimal('tien_mat', 15, 2)->default(0)->comment('Phần trả bằng tiền mặt');
            $table->decimal('tien_online', 15, 2)->default(0)->comment('Phần trả qua VNPay/Momo');
            $table->string('ma_giao_dich_online', 100)->nullable()->comment('Mã giao dịch VNPay/Momo');

            // Khuyến mãi
            $table->foreignId('khuyen_mai_id')->nullable()->constrained('khuyen_mais')->nullOnDelete();
            $table->string('ma_giam_gia', 50)->nullable()->comment('Mã code đã dùng');
            $table->enum('loai_giam', ['ma_code', 'rank', 'khong_giam'])->default('khong_giam');

            // Trạng thái
            $table->enum('trang_thai', ['cho_thanh_toan', 'da_thanh_toan', 'hoan_tien'])
                ->default('cho_thanh_toan')->index();

            // Người thu tiền
            $table->foreignId('nhan_vien_id')->nullable()->constrained('nhan_viens')->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();

            $table->dateTime('ngay_thanh_toan')->nullable();
            $table->text('ghi_chu')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('thanh_toans', function (Blueprint $table) {
            $table->dropColumn([
                'ma_thanh_toan', 'lich_hen_id', 'khach_hang_id',
                'tong_tien_goc', 'so_tien_giam', 'tong_tien_sau_giam',
                'hinh_thuc_thanh_toan', 'tien_mat', 'tien_online',
                'ma_giao_dich_online', 'khuyen_mai_id', 'ma_giam_gia',
                'loai_giam', 'trang_thai', 'nhan_vien_id', 'admin_id',
                'ngay_thanh_toan', 'ghi_chu',
            ]);
        });
    }
};