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
        Schema::create('chi_tiet_phieu_nhap_khos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phieu_nhap_kho_id')->constrained('phieu_nhap_khos')->onDelete('cascade');
            $table->foreignId('hang_hoa_id')->constrained('hang_hoas')->onDelete('cascade');
            $table->integer('so_luong');
            $table->string('so_lo', 50);
            $table->date('han_su_dung');
            $table->decimal('don_gia', 15, 2);
            $table->decimal('thanh_tien', 15, 2);
            $table->text('ghi_chu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_phieu_nhap_khos');
    }
};
