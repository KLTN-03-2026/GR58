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
        Schema::create('danh_muc_dich_vu_khoa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('danh_muc_dich_vu_id')->constrained('danh_muc_dich_vus')->cascadeOnDelete();
            $table->foreignId('khoa_id')->constrained('khoas')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['danh_muc_dich_vu_id', 'khoa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_muc_dich_vu_khoa');
    }
};
