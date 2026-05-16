<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lich_hens', function (Blueprint $table) {
            $table->boolean('da_thu_thuoc')->default(false)->after('da_thanh_toan');
        });
    }

    public function down(): void
    {
        Schema::table('lich_hens', function (Blueprint $table) {
            $table->dropColumn('da_thu_thuoc');
        });
    }
};
