<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Normalize default image URLs from full URLs to relative paths.
     * Pattern: http(s)://host[:port]/images/... -> images/...
     */
    public function up(): void
    {
        $regex = '#^https?://[^/]+/(images/.+)$#i';

        $tables = [
            ['table' => 'thu_cungs', 'column' => 'anh_dai_dien'],
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
                    }
                });

            Log::info("Migration normalize_default_image_urls: {$table}.{$column} - {$updated} records updated");
            echo "Updated {$updated} records in {$table}.{$column}\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new \LogicException('This migration is not reversible. Restore from backup if needed.');
    }
};
