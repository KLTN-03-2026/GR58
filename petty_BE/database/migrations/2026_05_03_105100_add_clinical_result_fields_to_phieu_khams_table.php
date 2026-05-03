<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phieu_khams', function (Blueprint $table) {
            $table->text('ket_qua_can_lam_sang')->nullable()->after('loai_chi_dinh');
            $table->json('tep_dinh_kem_can_lam_sang')->nullable()->after('ket_qua_can_lam_sang');
            $table->timestamp('thoi_gian_tra_ket_qua')->nullable()->after('tep_dinh_kem_can_lam_sang');
        });
    }

    public function down(): void
    {
        Schema::table('phieu_khams', function (Blueprint $table) {
            $table->dropColumn([
                'ket_qua_can_lam_sang',
                'tep_dinh_kem_can_lam_sang',
                'thoi_gian_tra_ket_qua',
            ]);
        });
    }
};
