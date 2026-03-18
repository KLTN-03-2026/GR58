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
        Schema::create('phieu_nhap_khos', function (Blueprint $table) {
            $table->id();
            $table->string('ma_phieu_nhap', 50)->unique();
            $table->date('ngay_nhap');
            $table->decimal('tong_tien', 15, 2)->default(0);
            $table->text('ghi_chu');
            $table->enum('trang_thai', ['cho_duyet', 'da_duyet', 'huy'])->default('cho_duyet');
            $table->foreignId('nha_cung_cap_id')->constrained('nha_cung_caps')->onDelete('cascade');
            $table->foreignId('phieu_chi_id')->constrained('phieu_chis')->onDelete('cascade');
            $table->foreignId('nhan_vien_id')->constrained('nhan_viens')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_nhap_khos');
    }
};
