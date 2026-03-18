<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chạy các migration.
     */
    public function up(): void
    {
        Schema::create('khach_hangs', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique(); 
            $table->string('phone')->nullable();
            $table->string('password')->nullable();
            $table->string('full_name');
            $table->text('address')->nullable();
            $table->enum('rank', ['Silver', 'Gold', 'Diamond'])->default('Silver');
            $table->string('anh_dai_dien')->nullable();
            $table->string('trang_thai')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Hoàn tác các migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('khach_hangs');
    }
};
