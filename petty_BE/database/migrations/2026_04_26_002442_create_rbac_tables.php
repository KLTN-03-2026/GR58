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
        // Bảng Roles (Vai trò)
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // admin, bac_si, y_ta, le_tan, tro_ly
            $table->string('display_name'); // Tên hiển thị
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Bảng Permissions (Quyền)
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // lich_hen.xem, lich_hen.tao, lich_hen.sua, lich_hen.xoa
            $table->string('display_name');
            $table->string('group')->nullable(); // lich_hen, tai_chinh, hang_hoa, etc.
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Bảng Role_Permission (Many-to-Many)
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            
            // Đảm bảo không trùng lặp
            $table->unique(['role_id', 'permission_id']);
        });

        // Thêm role_id vào bảng admins
        Schema::table('admins', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable()->after('phan_quyen_id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });

        // Thêm role_id vào bảng nhan_viens
        Schema::table('nhan_viens', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable()->after('phan_quyen_id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });

        // Thêm role_id vào bảng khach_hangs (nếu cần phân quyền cho khách hàng)
        Schema::table('khach_hangs', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable()->after('id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys và columns
        Schema::table('khach_hangs', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::table('nhan_viens', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        // Drop tables
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
