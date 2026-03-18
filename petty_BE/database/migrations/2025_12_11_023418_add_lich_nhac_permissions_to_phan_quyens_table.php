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
        Schema::table('phan_quyens', function (Blueprint $table) {
            // ========== NHÓM LỊCH NHẮC ==========
            $table->boolean('lich_nhac_xem')->default(false)->after('lich_lam_viec_tao');
            $table->boolean('lich_nhac_tao')->default(false)->after('lich_nhac_xem');
            $table->boolean('lich_nhac_sua')->default(false)->after('lich_nhac_tao');
            $table->boolean('lich_nhac_xoa')->default(false)->after('lich_nhac_sua');
            $table->boolean('lich_nhac_gui')->default(false)->after('lich_nhac_xoa');

            // ========== NHÓM HỒ SƠ BỆNH ÁN ==========
            $table->boolean('ho_so_benh_an_xem')->default(false)->after('thu_cung_xoa');
            $table->boolean('ho_so_benh_an_tao')->default(false)->after('ho_so_benh_an_xem');
            $table->boolean('ho_so_benh_an_sua')->default(false)->after('ho_so_benh_an_tao');
            $table->boolean('ho_so_benh_an_xoa')->default(false)->after('ho_so_benh_an_sua');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phan_quyens', function (Blueprint $table) {
            $table->dropColumn([
                'lich_nhac_xem',
                'lich_nhac_tao',
                'lich_nhac_sua',
                'lich_nhac_xoa',
                'lich_nhac_gui',
                'ho_so_benh_an_xem',
                'ho_so_benh_an_tao',
                'ho_so_benh_an_sua',
                'ho_so_benh_an_xoa',
            ]);
        });
    }
};
