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
        // Cập nhật comment của cột trang_thai để bao gồm tu_choi
        Schema::table('lich_dang_kys', function (Blueprint $table) {
            $table->string('trang_thai', 50)->default('chua_xac_nhan')->comment('Trạng thái: chua_xac_nhan, da_xac_nhan, tu_choi')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lich_dang_kys', function (Blueprint $table) {
            $table->string('trang_thai', 50)->default('chua_xac_nhan')->comment('Trạng thái: chua_xac_nhan, da_xac_nhan')->change();
        });
    }
};
