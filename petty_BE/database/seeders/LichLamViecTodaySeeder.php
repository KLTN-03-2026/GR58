<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LichLamViecTodaySeeder extends Seeder
{
    /**
     * Tạo lịch làm việc cho hôm nay và ngày mai
     * cho tất cả bác sĩ và y tá hiện có trong hệ thống.
     */
    public function run(): void
    {
        $today    = Carbon::today()->toDateString();
        $tomorrow = Carbon::tomorrow()->toDateString();
        $now      = Carbon::now();

        $phongs = ['Phòng Khám A', 'Phòng Khám B'];

        // Lấy tất cả bác sĩ
        $bacSis = DB::table('nhan_viens')
            ->where('vai_tro', 'bac_si')
            ->where('trang_thai', 'hoat_dong')
            ->pluck('id');

        // Lấy tất cả y tá
        $yTas = DB::table('nhan_viens')
            ->where('vai_tro', 'y_ta')
            ->where('trang_thai', 'hoat_dong')
            ->pluck('id');

        $records = [];

        foreach ([$today, $tomorrow] as $ngay) {
            // Bác sĩ: ca sáng + ca chiều luân phiên
            foreach ($bacSis as $index => $id) {
                $ca    = ($index % 2 === 0) ? 'ca_sang' : 'ca_chieu';
                $phong = $phongs[$index % count($phongs)];

                $exists = DB::table('lich_lam_viecs')
                    ->where('nhan_vien_id', $id)
                    ->where('ngay_lam', $ngay)
                    ->exists();

                if (!$exists) {
                    $records[] = [
                        'nhan_vien_id'   => $id,
                        'ngay_lam'       => $ngay,
                        'thoi_gian_truc' => $ca,
                        'phong_truc'     => $phong,
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ];
                }
            }

            // Y tá: ca sáng + ca chiều luân phiên
            foreach ($yTas as $index => $id) {
                $ca    = ($index % 2 === 0) ? 'ca_sang' : 'ca_chieu';
                $phong = $phongs[$index % count($phongs)];

                $exists = DB::table('lich_lam_viecs')
                    ->where('nhan_vien_id', $id)
                    ->where('ngay_lam', $ngay)
                    ->exists();

                if (!$exists) {
                    $records[] = [
                        'nhan_vien_id'   => $id,
                        'ngay_lam'       => $ngay,
                        'thoi_gian_truc' => $ca,
                        'phong_truc'     => $phong,
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ];
                }
            }
        }

        if (!empty($records)) {
            DB::table('lich_lam_viecs')->insert($records);
            $this->command->info('Đã tạo ' . count($records) . ' lịch làm việc cho ' . $today . ' và ' . $tomorrow);
        } else {
            $this->command->info('Tất cả lịch làm việc đã tồn tại, không cần thêm.');
        }
    }
}
