<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LichLamViecSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Xóa dữ liệu cũ và reset auto increment
        DB::table('lich_lam_viecs')->truncate();

        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ensure we have nhan_vien ids to reference. If none exist, create sample employees.
        $nhanVienIds = DB::table('nhan_viens')->pluck('id')->toArray();
        if (empty($nhanVienIds)) {
            $now = Carbon::now();
            $sampleNhanViens = [];
            for ($j = 1; $j <= 10; $j++) {
                $sampleNhanViens[] = [
                    'full_name' => "NV Sample $j",
                    'email' => "nv_sample{$j}@example.com",
                    'phone' => '0901000' . str_pad($j, 3, '0', STR_PAD_LEFT),
                    'address' => 'Địa chỉ mẫu',
                    'anh_dai_dien' => null,
                    'vai_tro' => ($j % 2 == 0) ? 'y_ta' : 'bac_si',
                    'chuc_danh' => 'Nhân viên mẫu',
                    'nam_kinh_nghiem' => rand(1, 10),
                    'chung_chi_hanh_nghe' => 'Chứng chỉ mẫu',
                    'bang_cap_chuyen_mon' => 'Bằng cấp mẫu',
                    'password' => Hash::make('password'),
                    'trang_thai' => 'hoat_dong',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            DB::table('nhan_viens')->insert($sampleNhanViens);
            $nhanVienIds = DB::table('nhan_viens')->pluck('id')->toArray();
        }

        $shifts = ['ca_sang', 'ca_chieu', 'ca_toi'];
        $phongs = ['Phòng Khám A', 'Phòng Khám B', 'Phòng Trực 1', 'Phòng Trực 2'];

        $samples = [];
        $start = Carbon::today();
        for ($i = 0; $i < 10; $i++) {
            $samples[] = [
                'ngay_lam' => $start->copy()->addDays($i)->toDateString(),
                'phong_truc' => $phongs[$i % count($phongs)],
                'thoi_gian_truc' => $shifts[$i % count($shifts)],
                'nhan_vien_id' => $nhanVienIds[$i % count($nhanVienIds)],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('lich_lam_viecs')->insert($samples);
    }
}
