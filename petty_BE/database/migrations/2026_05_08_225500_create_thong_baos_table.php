<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thong_baos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('khach_hang_id')->constrained('khach_hangs')->cascadeOnDelete();
            $table->enum('loai', ['lich_hen', 'tiem_phong', 'thanh_toan', 'ket_qua_kham']);
            $table->string('tieu_de', 255);
            $table->text('noi_dung');
            $table->boolean('da_doc')->default(false);
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();

            $table->index(['khach_hang_id', 'da_doc']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thong_baos');
    }
};
