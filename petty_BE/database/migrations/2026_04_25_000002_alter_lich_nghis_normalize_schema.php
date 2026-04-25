<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lich_nghis', function (Blueprint $table) {
            // Thêm cột ngay_nghi (date đơn) thay cho range ngay_bat_dau / ngay_ket_thuc
            if (! Schema::hasColumn('lich_nghis', 'ngay_nghi')) {
                $table->date('ngay_nghi')->after('nhan_vien_id')->nullable();
            }
        });

        // Đổi trang_thai sang ENUM mới
        \DB::statement("ALTER TABLE lich_nghis MODIFY trang_thai ENUM('cho_duyet','da_duyet','tu_choi') NOT NULL DEFAULT 'cho_duyet'");

        // Thêm unique constraint nếu chưa có
        try {
            \DB::statement('ALTER TABLE lich_nghis ADD UNIQUE KEY lich_nghis_nhan_vien_ngay_unique (nhan_vien_id, ngay_nghi)');
        } catch (\Exception $e) {
            // unique đã tồn tại
        }
    }

    public function down(): void
    {
        Schema::table('lich_nghis', function (Blueprint $table) {
            if (Schema::hasColumn('lich_nghis', 'ngay_nghi')) {
                $table->dropColumn('ngay_nghi');
            }
        });
        \DB::statement("ALTER TABLE lich_nghis MODIFY trang_thai ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
    }
};
