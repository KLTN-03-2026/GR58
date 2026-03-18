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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('ho_ten');
            $table->string('anh_dai_dien')->nullable();
            $table->string('mat_khau');
            $table->string('email')->unique();
            $table->string('so_dien_thoai')->nullable();
            $table->text('dia_chi')->nullable();
            $table->tinyInteger('trang_thai')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
