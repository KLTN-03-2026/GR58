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
        Schema::create('lich_lam_viecs', function (Blueprint $table) {
            $table->id();
            // Ngày làm (date), phòng trực, thời gian trực (chuỗi hoặc range)
            $table->date('ngay_lam');
            $table->string('phong_truc')->nullable();
            $table->string('thoi_gian_truc')->nullable();

            // Liên kết tới nhân viên
            $table->unsignedBigInteger('nhan_vien_id')->nullable();
            $table->foreign('nhan_vien_id')->references('id')->on('nhan_viens')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_lam_viecs');
    }
};
