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
        // Thêm cột phan_quyen_id vào bảng admins
        Schema::table('admins', function (Blueprint $table) {
            $table->unsignedBigInteger('phan_quyen_id')->nullable()->after('id');
            $table->foreign('phan_quyen_id')->references('id')->on('phan_quyens')->onDelete('set null');
        });

        // Thêm cột phan_quyen_id vào bảng nhan_viens
        Schema::table('nhan_viens', function (Blueprint $table) {
            $table->unsignedBigInteger('phan_quyen_id')->nullable()->after('id');
            $table->foreign('phan_quyen_id')->references('id')->on('phan_quyens')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropForeign(['phan_quyen_id']);
            $table->dropColumn('phan_quyen_id');
        });

        Schema::table('nhan_viens', function (Blueprint $table) {
            $table->dropForeign(['phan_quyen_id']);
            $table->dropColumn('phan_quyen_id');
        });
    }
};
