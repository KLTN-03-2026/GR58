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
        if (Schema::hasTable('dich_vus')) {
            Schema::table('dich_vus', function (Blueprint $table) {
                // code for service (optional unique code)
                $table->string('ma_dich_vu')->nullable()->unique()->after('ten');

                // detailed instructions or guidance for performing the service
                $table->text('huong_dan')->nullable()->after('mo_ta');

                // image path or filename for the service
                $table->string('anh_dich_vu')->nullable()->after('huong_dan');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('dich_vus')) {
            Schema::table('dich_vus', function (Blueprint $table) {
                // Drop columns if they exist
                if (Schema::hasColumn('dich_vus', 'ma_dich_vu')) {
                    $table->dropUnique(['ma_dich_vu']);
                    $table->dropColumn('ma_dich_vu');
                }
                if (Schema::hasColumn('dich_vus', 'huong_dan')) {
                    $table->dropColumn('huong_dan');
                }
                if (Schema::hasColumn('dich_vus', 'anh_dich_vu')) {
                    $table->dropColumn('anh_dich_vu');
                }
            });
        }
    }
};
