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
        Schema::create('hang_hoas', function (Blueprint $table) {
            $table->id();
            $table->string('ma_hang_hoa')->unique();
            $table->string('ten_mat_hang');
            $table->string('don_vi_tinh');
            $table->decimal('gia_von', 12, 2);
            $table->decimal('gia_ban', 12, 2);
            $table->integer('dinh_muc_toi_thieu')->default(0);
            $table->string('anh_san_pham')->nullable();
            $table->enum('tinh_trang', ['hoat_dong', 'ngung_kinh_doanh'])->default('hoat_dong');
            $table->foreignId('danh_muc_hang_hoa_id')->nullable()->constrained('danh_muc_hang_hoas')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hang_hoas');
    }
};
