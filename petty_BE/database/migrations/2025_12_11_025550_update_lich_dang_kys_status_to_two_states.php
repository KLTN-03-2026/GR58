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
        // Cập nhật các trạng thái cũ sang trạng thái mới
        DB::table('lich_dang_kys')
            ->where('trang_thai', 'cho_xac_nhan')
            ->update(['trang_thai' => 'chua_xac_nhan']);

        DB::table('lich_dang_kys')
            ->where('trang_thai', 'huy')
            ->update(['trang_thai' => 'chua_xac_nhan']);

        // Cập nhật comment của cột
        Schema::table('lich_dang_kys', function (Blueprint $table) {
            $table->string('trang_thai', 50)->default('chua_xac_nhan')->comment('Trạng thái: chua_xac_nhan, da_xac_nhan, tu_choi')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback về trạng thái cũ
        DB::table('lich_dang_kys')
            ->where('trang_thai', 'chua_xac_nhan')
            ->update(['trang_thai' => 'cho_xac_nhan']);

        Schema::table('lich_dang_kys', function (Blueprint $table) {
            $table->string('trang_thai', 50)->default('cho_xac_nhan')->comment('Trạng thái: cho_xac_nhan, da_xac_nhan, tu_choi, huy')->change();
        });
    }
};
