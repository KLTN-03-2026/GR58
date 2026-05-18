<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE khach_hangs MODIFY email VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::table('khach_hangs')
            ->whereNull('email')
            ->select('id')
            ->orderBy('id')
            ->chunkById(100, function ($customers): void {
                foreach ($customers as $customer) {
                    DB::table('khach_hangs')
                        ->where('id', $customer->id)
                        ->update([
                            'email' => sprintf('walkin-revert-%d@placeholder.local', $customer->id),
                        ]);
                }
            });

        DB::statement('ALTER TABLE khach_hangs MODIFY email VARCHAR(255) NOT NULL');
    }
};
