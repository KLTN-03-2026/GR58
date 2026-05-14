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
        Schema::table('ho_so_benh_ans', function (Blueprint $table) {
            $table->unique(['thu_cung_id', 'khach_hang_id'], 'ho_so_benh_ans_thu_cung_khach_hang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ho_so_benh_ans', function (Blueprint $table) {
            $table->dropUnique('ho_so_benh_ans_thu_cung_khach_hang_unique');
        });
    }
};
