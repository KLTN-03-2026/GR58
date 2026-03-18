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
        Schema::create('khuyen_mai_dich_vu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('khuyen_mai_id')->constrained('khuyen_mais')->onDelete('cascade');
            $table->foreignId('dich_vu_id')->constrained('dich_vus')->onDelete('cascade');
            $table->timestamps();

            // Đảm bảo mỗi dịch vụ chỉ được liên kết với một khuyến mãi một lần
            $table->unique(['khuyen_mai_id', 'dich_vu_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khuyen_mai_dich_vu');
    }
};
