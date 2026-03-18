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
        Schema::create('phan_loai_bai_viets', function (Blueprint $table) {
            $table->id();
            $table->string('ten_phan_loai', 255)->unique();
            $table->string('slug', 255)->unique()->index();
            $table->text('mo_ta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phan_loai_bai_viets');
    }
};
