<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing FK constraint on nhan_vien_id if exists
        $rows = DB::select("SELECT CONSTRAINT_NAME as fk FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lich_lam_viecs' AND COLUMN_NAME = 'nhan_vien_id' AND REFERENCED_TABLE_NAME = 'nhan_viens'");
        if (!empty($rows) && !empty($rows[0]->fk)) {
            $fk = $rows[0]->fk;
            DB::statement("ALTER TABLE `lich_lam_viecs` DROP FOREIGN KEY `" . $fk . "`");
        }

        // Modify columns: make phong_truc NOT NULL, thoi_gian_truc ENUM NOT NULL, nhan_vien_id NOT NULL
        DB::statement("ALTER TABLE `lich_lam_viecs` MODIFY `phong_truc` VARCHAR(255) NOT NULL");

        // Change thoi_gian_truc to ENUM with the three shifts
        DB::statement("ALTER TABLE `lich_lam_viecs` MODIFY `thoi_gian_truc` ENUM('ca_sang','ca_chieu','ca_toi') NOT NULL");

        // Make nhan_vien_id NOT NULL
        DB::statement("ALTER TABLE `lich_lam_viecs` MODIFY `nhan_vien_id` BIGINT UNSIGNED NOT NULL");

        // Add foreign key back with ON DELETE CASCADE
        DB::statement("ALTER TABLE `lich_lam_viecs` ADD CONSTRAINT `fk_lich_lam_viecs_nhan_vien_id` FOREIGN KEY (`nhan_vien_id`) REFERENCES `nhan_viens`(`id`) ON DELETE CASCADE");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the FK we added
        $rows = DB::select("SELECT CONSTRAINT_NAME as fk FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lich_lam_viecs' AND COLUMN_NAME = 'nhan_vien_id' AND REFERENCED_TABLE_NAME = 'nhan_viens'");
        if (!empty($rows) && !empty($rows[0]->fk)) {
            $fk = $rows[0]->fk;
            DB::statement("ALTER TABLE `lich_lam_viecs` DROP FOREIGN KEY `" . $fk . "`");
        }

        // Revert columns to previous nullable types
        DB::statement("ALTER TABLE `lich_lam_viecs` MODIFY `phong_truc` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `lich_lam_viecs` MODIFY `thoi_gian_truc` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `lich_lam_viecs` MODIFY `nhan_vien_id` BIGINT UNSIGNED NULL");

        // Restore foreign key with ON DELETE SET NULL
        DB::statement("ALTER TABLE `lich_lam_viecs` ADD CONSTRAINT `fk_lich_lam_viecs_nhan_vien_id` FOREIGN KEY (`nhan_vien_id`) REFERENCES `nhan_viens`(`id`) ON DELETE SET NULL");
    }
};
