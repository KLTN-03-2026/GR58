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
        Schema::create('lich_hens', function (Blueprint $table) {
            $table->id();
            // appointment datetime
            $table->dateTime('ngay_gio');

            // notes and guidance
            $table->text('ghi_chu')->nullable();
            $table->text('huong_dan')->nullable();

            // status (e.g. pending, confirmed, completed, cancelled)
            $table->string('trang_thai', 50)->default('pending')->index();

            // additional fields
            $table->string('nguon_goc', 100)->nullable()->comment('Nguồn gốc đặt lịch (online, phone, walk-in, etc.)');
            $table->dateTime('thoi_gian_checkin')->nullable()->comment('Thời gian khách hàng check-in');
            $table->dateTime('thoi_gian_bat_dau_kham')->nullable()->comment('Thời gian bắt đầu khám');
            $table->dateTime('thoi_gian_hoan_thanh')->nullable()->comment('Thời gian hoàn thành khám');

            // relationships
            // customer (khach_hangs) - should exist earlier
            $table->foreignId('khach_hang_id')->constrained('khach_hangs')->cascadeOnDelete();

            // pet (thu_cungs)
            $table->foreignId('thu_cung_id')->constrained('thu_cungs')->cascadeOnDelete();

            // service, payment, staff - optional relationships (create FK in separate migration
            // so referenced tables that may be created later won't break initial migrate)
            $table->unsignedBigInteger('dich_vu_id')->nullable()->index();
            $table->unsignedBigInteger('thanh_toan_id')->nullable()->index();
            $table->unsignedBigInteger('nhan_vien_id')->nullable()->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_hens');
    }
};
