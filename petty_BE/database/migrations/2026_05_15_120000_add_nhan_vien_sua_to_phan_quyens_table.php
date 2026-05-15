<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phan_quyens', function (Blueprint $table) {
            if (! Schema::hasColumn('phan_quyens', 'nhan_vien_sua')) {
                $table->boolean('nhan_vien_sua')->default(false)->after('nhan_vien_tao');
            }
        });

        // Grant the new permission to existing Admin role so admin can edit staff right away.
        try {
            DB::table('phan_quyens')
                ->where('ma_vai_tro', 'Admin')
                ->update(['nhan_vien_sua' => true]);
        } catch (\Throwable $e) {
            // ignore if seeded role not present
        }
    }

    public function down(): void
    {
        Schema::table('phan_quyens', function (Blueprint $table) {
            if (Schema::hasColumn('phan_quyens', 'nhan_vien_sua')) {
                $table->dropColumn('nhan_vien_sua');
            }
        });
    }
};
