<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chi_tiet_khuyen_mais', function (Blueprint $table) {
            // Hoàn thiện bảng chi tiết khuyến mãi (ghi lại lịch sử dùng mã)
            $table->foreignId('thanh_toan_id')->nullable()->constrained('thanh_toans')->nullOnDelete()->after('id');
            $table->foreignId('khuyen_mai_id')->nullable()->constrained('khuyen_mais')->nullOnDelete()->after('thanh_toan_id');
            $table->foreignId('khach_hang_id')->nullable()->constrained('khach_hangs')->nullOnDelete()->after('khuyen_mai_id');
            $table->decimal('so_tien_giam', 15, 2)->default(0)->after('khach_hang_id');
            $table->dateTime('ngay_su_dung')->nullable()->after('so_tien_giam');
        });
    }

    public function down(): void
    {
        Schema::table('chi_tiet_khuyen_mais', function (Blueprint $table) {
            $table->dropForeign(['thanh_toan_id']);
            $table->dropForeign(['khuyen_mai_id']);
            $table->dropForeign(['khach_hang_id']);
            $table->dropColumn(['thanh_toan_id', 'khuyen_mai_id', 'khach_hang_id', 'so_tien_giam', 'ngay_su_dung']);
        });
    }
};