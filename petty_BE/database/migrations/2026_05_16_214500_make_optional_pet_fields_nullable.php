<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE thu_cungs MODIFY tuoi_thu_cung DATE NULL');
        DB::statement('ALTER TABLE thu_cungs MODIFY gioi_tinh VARCHAR(255) NULL');
        DB::statement('ALTER TABLE thu_cungs MODIFY can_nang VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::table('thu_cungs')
            ->whereNull('tuoi_thu_cung')
            ->update(['tuoi_thu_cung' => '2000-01-01']);

        DB::table('thu_cungs')
            ->whereNull('gioi_tinh')
            ->update(['gioi_tinh' => 'duc']);

        DB::table('thu_cungs')
            ->whereNull('can_nang')
            ->update(['can_nang' => '0']);

        DB::statement('ALTER TABLE thu_cungs MODIFY tuoi_thu_cung DATE NOT NULL');
        DB::statement('ALTER TABLE thu_cungs MODIFY gioi_tinh VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE thu_cungs MODIFY can_nang VARCHAR(255) NOT NULL');
    }
};
