<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lich_nghis', function (Blueprint $table) {
            // co_luong = trong quota 4 ngày/tháng; khong_luong = vượt quota
            $table->enum('loai_nghi', ['co_luong', 'khong_luong'])
                  ->default('co_luong')
                  ->after('trang_thai');
        });
    }

    public function down(): void
    {
        Schema::table('lich_nghis', function (Blueprint $table) {
            $table->dropColumn('loai_nghi');
        });
    }
};
