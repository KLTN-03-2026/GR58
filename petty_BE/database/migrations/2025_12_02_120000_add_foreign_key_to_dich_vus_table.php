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
        // Only add the FK if both tables exist and the column is present
        if (Schema::hasTable('dich_vus') && Schema::hasTable('danh_muc_dich_vus')) {
            Schema::table('dich_vus', function (Blueprint $table) {
                // add foreign key constraint on danh_muc_id -> danh_muc_dich_vus.id
                // the column `danh_muc_id` was left as unsignedBigInteger(nullable) in the original migration
                $table->foreign('danh_muc_id')
                    ->references('id')
                    ->on('danh_muc_dich_vus')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('dich_vus')) {
            Schema::table('dich_vus', function (Blueprint $table) {
                // drop foreign key if it exists
                try {
                    $table->dropForeign(['danh_muc_id']);
                } catch (\Exception $e) {
                    // ignore if the foreign key does not exist
                }
            });
        }
    }
};
