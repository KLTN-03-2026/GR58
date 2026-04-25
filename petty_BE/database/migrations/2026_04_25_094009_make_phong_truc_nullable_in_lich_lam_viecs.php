<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * phong_truc is no longer required (room concept removed from UI).
     * Allow NULL so inserts without phong_truc do not crash.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `lich_lam_viecs` MODIFY `phong_truc` VARCHAR(255) NULL DEFAULT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `lich_lam_viecs` MODIFY `phong_truc` VARCHAR(255) NOT NULL");
    }
};
