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
        Schema::create('bai_viets', function (Blueprint $table) {
            $table->id();
            $table->string('ten_bai_viet', 255);
            $table->string('slug', 255)->unique()->index();
            $table->longText('noi_dung');
            $table->text('mo_ta')->nullable();
            $table->string('anh_bai_viet', 255)->nullable();
            $table->enum('trang_thai', ['published', 'hidden'])->default('published')->index();

            // Foreign keys
            $table->unsignedBigInteger('nhan_vien_id')->nullable()->index();
            $table->unsignedBigInteger('phan_loai_bai_viet_id')->nullable()->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bai_viets');
    }
};
