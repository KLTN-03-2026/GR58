<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dich_vus', function (Blueprint $table) {
            $table->boolean('yeu_cau_tt_truoc')->default(false)->after('trang_thai');
        });
    }

    public function down(): void
    {
        Schema::table('dich_vus', function (Blueprint $table) {
            $table->dropColumn('yeu_cau_tt_truoc');
        });
    }
};
