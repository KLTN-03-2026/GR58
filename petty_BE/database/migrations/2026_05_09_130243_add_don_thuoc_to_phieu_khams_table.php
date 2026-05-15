<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phieu_khams', function (Blueprint $table) {
            $table->json('don_thuoc')->nullable()->after('loai_chi_dinh')->comment('Danh sách thuốc trong đơn (JSON)');
        });
    }

    public function down(): void
    {
        Schema::table('phieu_khams', function (Blueprint $table) {
            $table->dropColumn('don_thuoc');
        });
    }
};
