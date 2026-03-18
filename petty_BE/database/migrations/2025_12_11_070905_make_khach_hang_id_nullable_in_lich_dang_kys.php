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
        Schema::table('lich_dang_kys', function (Blueprint $table) {
            // Làm cho khach_hang_id nullable
            $table->foreignId('khach_hang_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lich_dang_kys', function (Blueprint $table) {
            $table->foreignId('khach_hang_id')->nullable(false)->change();
        });
    }
};
