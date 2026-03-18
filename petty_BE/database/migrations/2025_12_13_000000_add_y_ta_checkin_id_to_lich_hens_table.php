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
        Schema::table('lich_hens', function (Blueprint $table) {
            $table->unsignedBigInteger('y_ta_checkin_id')->nullable()->after('thoi_gian_checkin')->comment('ID y tá thực hiện check-in');

            // Thêm foreign key constraint
            $table->foreign('y_ta_checkin_id', 'fk_lich_hen_y_ta_checkin')
                ->references('id')
                ->on('nhan_viens')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lich_hens', function (Blueprint $table) {
            $table->dropForeign('fk_lich_hen_y_ta_checkin');
            $table->dropColumn('y_ta_checkin_id');
        });
    }
};
