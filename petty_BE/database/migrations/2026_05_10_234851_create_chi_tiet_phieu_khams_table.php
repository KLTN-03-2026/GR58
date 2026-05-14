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
        Schema::create('chi_tiet_phieu_khams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phieu_kham_id')->constrained('phieu_khams')->cascadeOnDelete();
            $table->foreignId('hang_hoa_id')->constrained('hang_hoas')->restrictOnDelete();
            $table->integer('so_luong')->default(1);
            $table->string('don_vi', 50)->nullable();
            $table->string('lieu_su_dung', 100)->nullable();
            $table->string('tan_suat', 100)->nullable();
            $table->string('thoi_gian_dung', 100)->nullable();
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
            $table->index('phieu_kham_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_phieu_khams');
    }
};
