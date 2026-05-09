<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dinh_kem_phieu_khams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phieu_kham_id')
                ->constrained('phieu_khams')
                ->cascadeOnDelete();
            $table->string('ten_file', 255);
            $table->string('duong_dan', 500);
            $table->string('loai_mime', 100);
            $table->unsignedInteger('kich_thuoc');
            $table->unsignedBigInteger('nguoi_upload_id')->nullable();
            $table->foreign('nguoi_upload_id')
                ->references('id')
                ->on('nhan_viens')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dinh_kem_phieu_khams');
    }
};
