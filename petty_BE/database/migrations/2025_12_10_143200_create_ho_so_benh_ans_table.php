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
        Schema::create('ho_so_benh_ans', function (Blueprint $table) {
            $table->id();

            // Mã bệnh án
            $table->string('ma_benh_an', 50)->unique()->comment('Mã bệnh án duy nhất');

            // Nội dung bệnh án
            $table->text('noi_dung')->comment('Nội dung chi tiết bệnh án');

            // Quan hệ
            $table->foreignId('khach_hang_id')->nullable()->constrained('khach_hangs')->nullOnDelete();
            $table->foreignId('thu_cung_id')->nullable()->constrained('thu_cungs')->nullOnDelete();
            $table->foreignId('nhan_vien_id')->nullable()->constrained('nhan_viens')->nullOnDelete();

            $table->timestamps();

            // Index
            $table->index('ma_benh_an');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ho_so_benh_ans');
    }
};
