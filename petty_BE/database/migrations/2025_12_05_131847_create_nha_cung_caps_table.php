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
        Schema::create('nha_cung_caps', function (Blueprint $table) {
            $table->id();
            $table->string('ma_nha_cung_cap', 50)->unique();
            $table->string('ten_nha_cung_cap');
            $table->string('ten_nguoi_lien_he');
            $table->string('so_dien_thoai', 15);
            $table->text('dia_chi');
            $table->string('email')->unique();
            $table->string('ma_so_thue', 50);
            $table->text('mo_ta');
            $table->enum('trang_thai', ['hoat_dong', 'ngung_hoat_dong'])->default('hoat_dong');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nha_cung_caps');
    }
};
