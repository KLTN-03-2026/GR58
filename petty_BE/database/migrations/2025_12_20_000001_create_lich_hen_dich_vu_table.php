<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lich_hen_dich_vu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lich_hen_id')->constrained('lich_hens')->cascadeOnDelete();
            $table->foreignId('dich_vu_id')->constrained('dich_vus')->cascadeOnDelete();
            $table->unsignedInteger('so_luong')->default(1);
            $table->decimal('don_gia', 15, 2)->comment('Giá tại thời điểm đặt');
            $table->decimal('thanh_tien', 15, 2)->comment('so_luong × don_gia');
            $table->timestamps();

            $table->unique(['lich_hen_id', 'dich_vu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lich_hen_dich_vu');
    }
};