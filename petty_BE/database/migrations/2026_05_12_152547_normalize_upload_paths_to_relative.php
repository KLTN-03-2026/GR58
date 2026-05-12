<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Normalize upload paths from full URLs to relative paths.
     * Pattern: http(s)://host[:port]/storage/path -> path
     * Does NOT touch external URLs (e.g., Google OAuth avatars).
     */
    public function up(): void
    {
        $regex = '#^https?://[^/]+/storage/(.+)$#i';

        $tables = [
            ['table' => 'khach_hangs', 'column' => 'anh_dai_dien'],
            ['table' => 'nhan_viens', 'column' => 'anh_dai_dien'],
            ['table' => 'thu_cungs', 'column' => 'anh_dai_dien'],
            ['table' => 'admins', 'column' => 'anh_dai_dien'],
            ['table' => 'dich_vus', 'column' => 'anh_dich_vu'],
        ];

        foreach ($tables as $config) {
            $table = $config['table'];
            $column = $config['column'];
            $updated = 0;

            DB::table($table)
                ->whereNotNull($column)
                ->where($column, 'like', 'http%')
                ->orderBy('id')
                ->chunkById(200, function ($rows) use ($table, $column, $regex, &$updated) {
                    foreach ($rows as $row) {
                        $value = $row->{$column};
                        if (preg_match($regex, $value, $matches)) {
                            DB::table($table)
                                ->where('id', $row->id)
                                ->update([$column => $matches[1]]);
                            $updated++;
                        }
                        // else: external URL (e.g., Google OAuth) - skip
                    }
                });

            Log::info("Migration normalize_upload_paths: {$table}.{$column} - {$updated} records updated");
            echo "Updated {$updated} records in {$table}.{$column}\n";
        }
    }

    /**
     * Reverse the migrations.
     *
     * This migration is not reversible. Restore from backup if needed.
     */
    public function down(): void
    {
        throw new \LogicException('This migration is not reversible. Restore from backup if needed.');
    }
};
