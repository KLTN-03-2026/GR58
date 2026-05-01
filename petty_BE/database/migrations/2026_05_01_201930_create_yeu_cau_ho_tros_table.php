<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('yeu_cau_ho_tros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('khach_hang_id')->nullable()->constrained('khach_hangs')->nullOnDelete();
            $table->string('ho_ten');
            $table->string('email');
            $table->string('so_dien_thoai')->nullable();
            $table->string('chu_de');
            $table->text('noi_dung');
            $table->enum('trang_thai', ['chua_xu_ly', 'dang_xu_ly', 'da_xu_ly'])->default('chua_xu_ly');
            $table->text('phan_hoi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yeu_cau_ho_tros');
    }
};
