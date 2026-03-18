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
        Schema::create('thu_cungs', function (Blueprint $table) {
            $table->id();
            $table->string('anh_dai_dien')->nullable();
            $table->string('ten_thu_cung');
            $table->string('loai_thu_cung');
            $table->string('giong_thu_cung');
            // store birthdate / age as a date (YYYY-MM-DD)
            $table->date('tuoi_thu_cung');
            $table->string('gioi_tinh');
            $table->string('can_nang');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thu_cungs');
    }
};
