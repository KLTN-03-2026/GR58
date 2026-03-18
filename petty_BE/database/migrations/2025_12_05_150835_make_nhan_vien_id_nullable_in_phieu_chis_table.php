<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('phieu_chis', function (Blueprint $table) {
            $table->foreignId('nhan_vien_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Bước 1: Điền giá trị mặc định cho các bản ghi có nhan_vien_id = NULL
        // Bạn có thể thay 0 bằng một ID nhân viên hợp lệ thực tế tồn tại trong bảng users/nhan_viens
        DB::statement("UPDATE phieu_chis SET nhan_vien_id = 0 WHERE nhan_vien_id IS NULL");

        // Bước 2: Sau khi chắc chắn không còn NULL, mới đổi cột thành NOT NULL
        Schema::table('phieu_chis', function (Blueprint $table) {
            $table->foreignId('nhan_vien_id')->nullable(false)->change();
        });
    }
};
