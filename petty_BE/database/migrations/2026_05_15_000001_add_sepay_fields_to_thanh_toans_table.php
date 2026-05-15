<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE thanh_toans MODIFY COLUMN hinh_thuc_thanh_toan ENUM('tien_mat','vnpay','momo','chuyen_khoan','ket_hop') DEFAULT 'tien_mat'");
        DB::statement("ALTER TABLE thanh_toans MODIFY COLUMN trang_thai ENUM('cho_thanh_toan','da_thanh_toan','hoan_tien','het_han') DEFAULT 'cho_thanh_toan'");

        Schema::table('thanh_toans', function (Blueprint $table) {
            $table->string('sepay_transaction_id', 100)->nullable()->after('ma_giao_dich_online');
            $table->timestamp('het_han_luc')->nullable()->after('ngay_thanh_toan');
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE thanh_toans MODIFY COLUMN hinh_thuc_thanh_toan ENUM('tien_mat','vnpay','momo','ket_hop') DEFAULT 'tien_mat'");
        DB::statement("ALTER TABLE thanh_toans MODIFY COLUMN trang_thai ENUM('cho_thanh_toan','da_thanh_toan','hoan_tien') DEFAULT 'cho_thanh_toan'");

        Schema::table('thanh_toans', function (Blueprint $table) {
            $table->dropColumn(['sepay_transaction_id', 'het_han_luc']);
        });
    }
};
