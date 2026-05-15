<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('lich_hens')
            ->whereNotNull('dich_vu_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('lich_hen_dich_vu')
                    ->whereColumn('lich_hen_dich_vu.lich_hen_id', 'lich_hens.id')
                    ->whereColumn('lich_hen_dich_vu.dich_vu_id', 'lich_hens.dich_vu_id');
            })
            ->orderBy('id')
            ->chunk(500, function ($appointments) {
                $rows = [];
                foreach ($appointments as $appointment) {
                    $service = DB::table('dich_vus')->find($appointment->dich_vu_id);
                    if (!$service) {
                        continue;
                    }
                    $rows[] = [
                        'lich_hen_id' => $appointment->id,
                        'dich_vu_id' => $appointment->dich_vu_id,
                        'so_luong' => 1,
                        'don_gia' => $service->gia_tien,
                        'thanh_tien' => $service->gia_tien,
                        'created_at' => $appointment->created_at ?? now(),
                        'updated_at' => now(),
                    ];
                }
                if (!empty($rows)) {
                    DB::table('lich_hen_dich_vu')->insert($rows);
                }
            });
    }

    public function down(): void
    {
        DB::statement('
            DELETE lhdv FROM lich_hen_dich_vu lhdv
            INNER JOIN lich_hens lh ON lh.id = lhdv.lich_hen_id
            WHERE lhdv.dich_vu_id = lh.dich_vu_id
            AND lhdv.so_luong = 1
        ');
    }
};
