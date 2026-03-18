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
        Schema::table('bai_viets', function (Blueprint $table) {
            // Foreign key to nhan_vien
            if (Schema::hasTable('nhan_viens')) {
                $table->foreign('nhan_vien_id')
                    ->references('id')
                    ->on('nhan_viens')
                    ->onDelete('set null');
            }

            // Foreign key to phan_loai_bai_viet
            if (Schema::hasTable('phan_loai_bai_viets')) {
                $table->foreign('phan_loai_bai_viet_id')
                    ->references('id')
                    ->on('phan_loai_bai_viets')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bai_viets', function (Blueprint $table) {
            try {
                $table->dropForeign('bai_viets_nhan_vien_id_foreign');
            } catch (\Exception $e) {
                // Foreign key doesn't exist, skip
            }

            try {
                $table->dropForeign('bai_viets_phan_loai_bai_viet_id_foreign');
            } catch (\Exception $e) {
                // Foreign key doesn't exist, skip
            }
        });
    }
};
