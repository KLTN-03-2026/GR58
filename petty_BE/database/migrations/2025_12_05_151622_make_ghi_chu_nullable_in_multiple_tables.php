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
        // Make ghi_chu nullable in phieu_nhap_khos
        Schema::table('phieu_nhap_khos', function (Blueprint $table) {
            $table->text('ghi_chu')->nullable()->change();
        });

        // Make ghi_chu nullable in phieu_chis
        Schema::table('phieu_chis', function (Blueprint $table) {
            $table->text('ghi_chu')->nullable()->change();
        });

        // Make ghi_chu nullable in chi_tiet_phieu_nhap_khos
        Schema::table('chi_tiet_phieu_nhap_khos', function (Blueprint $table) {
            $table->text('ghi_chu')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert ghi_chu to not nullable in phieu_nhap_khos
        Schema::table('phieu_nhap_khos', function (Blueprint $table) {
            $table->text('ghi_chu')->nullable(false)->change();
        });

        // Revert ghi_chu to not nullable in phieu_chis
        Schema::table('phieu_chis', function (Blueprint $table) {
            $table->text('ghi_chu')->nullable(false)->change();
        });

        // Revert ghi_chu to not nullable in chi_tiet_phieu_nhap_khos
        Schema::table('chi_tiet_phieu_nhap_khos', function (Blueprint $table) {
            $table->text('ghi_chu')->nullable(false)->change();
        });
    }
};
