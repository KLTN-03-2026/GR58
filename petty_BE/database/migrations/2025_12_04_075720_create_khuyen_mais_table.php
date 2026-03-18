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
        Schema::create('khuyen_mais', function (Blueprint $table) {
            $table->id();
            $table->string('ten_khuyen_mai'); // Tên khuyến mãi
            $table->text('mo_ta')->nullable(); // Mô tả
            $table->enum('loai_khuyen_mai', ['ma_giam_gia', 'chuong_trinh_tu_dong'])->default('ma_giam_gia'); // Loại khuyến mãi

            // Trường cho mã giảm giá (chỉ áp dụng khi loai_khuyen_mai = 'ma_giam_gia')
            $table->string('ma_code')->unique()->nullable(); // Mã code
            $table->decimal('gia_tri_don_toi_thieu', 15, 2)->nullable(); // Giá trị đơn tối thiểu

            // Loại khách hàng
            $table->enum('loai_khach_hang', ['tat_ca', 'vip'])->default('tat_ca'); // Loại khách hàng

            // Hình thức giảm
            $table->enum('hinh_thuc_giam', ['phan_tram', 'so_tien'])->default('phan_tram'); // Hình thức giảm
            $table->decimal('giam_toi_da', 15, 2)->nullable(); // Giảm tối đa (khi chọn phần trăm)
            $table->decimal('gia_tri_giam', 15, 2); // Giá trị giảm (% hoặc số tiền)

            // Thời gian
            $table->dateTime('tu_ngay'); // Từ ngày
            $table->dateTime('den_ngay'); // Đến ngày

            // Giới hạn sử dụng
            $table->integer('tong_so_luong')->nullable(); // Tổng số lượng mã giảm
            $table->integer('so_luong_da_dung')->default(0); // Số lượng đã dùng
            $table->integer('gioi_han_moi_khach')->nullable(); // Giới hạn mỗi khách

            // Trạng thái
            $table->enum('trang_thai', ['dang_chay', 'da_ket_thuc'])->default('dang_chay'); // Trạng thái khuyến mãi

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khuyen_mais');
    }
};
