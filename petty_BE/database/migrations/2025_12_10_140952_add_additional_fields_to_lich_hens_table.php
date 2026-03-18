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
        // Columns already exist in the create_lich_hens_table migration
        // This migration is intentionally empty to avoid duplicate column errors
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lich_hens', function (Blueprint $table) {
            $table->dropColumn(['nguon_goc', 'thoi_gian_checkin', 'thoi_gian_bat_dau_kham', 'thoi_gian_hoan_thanh']);
        });
    }
};
