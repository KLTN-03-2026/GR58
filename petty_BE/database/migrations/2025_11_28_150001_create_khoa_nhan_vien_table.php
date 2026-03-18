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
        Schema::create('khoa_nhan_vien', function (Blueprint $table) {
            $table->id();
            $table->foreignId('khoa_id')->constrained('khoas')->cascadeOnDelete();
            $table->foreignId('nhan_vien_id')->constrained('nhan_viens')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['khoa_id', 'nhan_vien_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khoa_nhan_vien');
    }
};
