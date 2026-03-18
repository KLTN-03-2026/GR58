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
        Schema::table('lich_hens', function (Blueprint $table) {
            // add foreign keys for tables that are created after the original migration
            if (Schema::hasColumn('lich_hens', 'dich_vu_id') && Schema::hasTable('dich_vus')) {
                $table->foreign('dich_vu_id')->references('id')->on('dich_vus')->nullOnDelete();
            }

            if (Schema::hasColumn('lich_hens', 'thanh_toan_id') && Schema::hasTable('thanh_toans')) {
                $table->foreign('thanh_toan_id')->references('id')->on('thanh_toans')->nullOnDelete();
            }

            if (Schema::hasColumn('lich_hens', 'nhan_vien_id') && Schema::hasTable('nhan_viens')) {
                $table->foreign('nhan_vien_id')->references('id')->on('nhan_viens')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lich_hens', function (Blueprint $table) {
            if (Schema::hasColumn('lich_hens', 'dich_vu_id')) {
                $table->dropForeign(['dich_vu_id']);
            }
            if (Schema::hasColumn('lich_hens', 'thanh_toan_id')) {
                $table->dropForeign(['thanh_toan_id']);
            }
            if (Schema::hasColumn('lich_hens', 'nhan_vien_id')) {
                $table->dropForeign(['nhan_vien_id']);
            }
        });
    }
};
