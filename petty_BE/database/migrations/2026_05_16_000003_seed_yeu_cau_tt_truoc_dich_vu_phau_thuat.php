<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('dich_vus')
            ->where('id', 17)
            ->update(['yeu_cau_tt_truoc' => true]);
    }

    public function down(): void
    {
        DB::table('dich_vus')
            ->where('id', 17)
            ->update(['yeu_cau_tt_truoc' => false]);
    }
};
