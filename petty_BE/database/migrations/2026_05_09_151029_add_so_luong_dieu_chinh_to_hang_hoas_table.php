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
        Schema::table('hang_hoas', function (Blueprint $table) {
            // Lưu tổng chênh lệch cộng dồn từ các lần kiểm kê (có thể âm khi kho thực tế ít hơn hệ thống)
            $table->integer('so_luong_dieu_chinh')->default(0)->after('tinh_trang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hang_hoas', function (Blueprint $table) {
            $table->dropColumn('so_luong_dieu_chinh');
        });
    }
};
