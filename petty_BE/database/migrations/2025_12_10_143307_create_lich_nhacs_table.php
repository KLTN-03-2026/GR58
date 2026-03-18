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
        Schema::create('lich_nhacs', function (Blueprint $table) {
            $table->id();

            // Ngày nhắc
            $table->dateTime('ngay_nhac')->comment('Ngày giờ nhắc nhở');

            // Phân loại
            $table->string('phan_loai', 100)->comment('Loại nhắc nhở: tái khám, tiêm phòng, xét nghiệm, thuốc, khác');

            // Nội dung và ghi chú
            $table->text('noi_dung')->comment('Nội dung nhắc nhở');
            $table->text('ghi_chu')->nullable()->comment('Ghi chú thêm');

            // Trạng thái
            $table->string('trang_thai', 50)->default('chua_gui')->comment('Trạng thái: chua_gui, da_gui, hoan_thanh, huy');

            // Quan hệ với hồ sơ bệnh án
            $table->foreignId('ho_so_benh_an_id')->constrained('ho_so_benh_ans')->cascadeOnDelete();

            $table->timestamps();

            // Index để tăng hiệu suất truy vấn
            $table->index('ngay_nhac');
            $table->index('trang_thai');
            $table->index('phan_loai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_nhacs');
    }
};
