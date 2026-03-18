<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThuCungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Temporarily disable foreign key checks so we can truncate when other tables
        // (like lich_hens) reference this table.
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('thu_cungs')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        DB::table('thu_cungs')->insert([
            [
                'anh_dai_dien' => 'bong.jpg',
                'ten_thu_cung' => 'Bông',
                'loai_thu_cung' => 'Chó',
                'giong_thu_cung' => 'Poodle',
                // birthdate (YYYY-MM-DD)
                'tuoi_thu_cung' => '2022-06-01',
                'gioi_tinh' => 'Cái',
                'can_nang' => '4.5 kg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'anh_dai_dien' => 'mun.jpg',
                'ten_thu_cung' => 'Mun',
                'loai_thu_cung' => 'Mèo',
                'giong_thu_cung' => 'Mèo Ta',
                'tuoi_thu_cung' => '2023-09-10',
                'gioi_tinh' => 'Đực',
                'can_nang' => '3.2 kg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'anh_dai_dien' => 'vang.jpg',
                'ten_thu_cung' => 'Vàng',
                'loai_thu_cung' => 'Chó',
                'giong_thu_cung' => 'Labrador',
                'tuoi_thu_cung' => '2021-03-15',
                'gioi_tinh' => 'Đực',
                'can_nang' => '28 kg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'anh_dai_dien' => 'beo.jpg',
                'ten_thu_cung' => 'Béo',
                'loai_thu_cung' => 'Mèo',
                'giong_thu_cung' => 'Ba Tư',
                'tuoi_thu_cung' => '2024-08-20',
                'gioi_tinh' => 'Cái',
                'can_nang' => '2.8 kg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'anh_dai_dien' => 'luc.jpg',
                'ten_thu_cung' => 'Lục',
                'loai_thu_cung' => 'Chó',
                'giong_thu_cung' => 'Shiba',
                'tuoi_thu_cung' => '2023-11-01',
                'gioi_tinh' => 'Đực',
                'can_nang' => '9 kg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'anh_dai_dien' => 'nho.jpg',
                'ten_thu_cung' => 'Nhỏ',
                'loai_thu_cung' => 'Chó',
                'giong_thu_cung' => 'Chihuahua',
                'tuoi_thu_cung' => '2024-01-05',
                'gioi_tinh' => 'Cái',
                'can_nang' => '2.0 kg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'anh_dai_dien' => 'hat.jpg',
                'ten_thu_cung' => 'Hạt',
                'loai_thu_cung' => 'Mèo',
                'giong_thu_cung' => 'Munchkin',
                'tuoi_thu_cung' => '2022-11-11',
                'gioi_tinh' => 'Đực',
                'can_nang' => '4.0 kg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'anh_dai_dien' => 'soi.jpg',
                'ten_thu_cung' => 'Sói',
                'loai_thu_cung' => 'Chó',
                'giong_thu_cung' => 'Husky',
                'tuoi_thu_cung' => '2020-04-02',
                'gioi_tinh' => 'Đực',
                'can_nang' => '22 kg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'anh_dai_dien' => 'kim.jpg',
                'ten_thu_cung' => 'Kim',
                'loai_thu_cung' => 'Mèo',
                'giong_thu_cung' => 'Sphynx',
                'tuoi_thu_cung' => '2023-02-18',
                'gioi_tinh' => 'Cái',
                'can_nang' => '3.5 kg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'anh_dai_dien' => 'hoa.jpg',
                'ten_thu_cung' => 'Hoa',
                'loai_thu_cung' => 'Chó',
                'giong_thu_cung' => 'Corgi',
                'tuoi_thu_cung' => '2022-09-09',
                'gioi_tinh' => 'Cái',
                'can_nang' => '11 kg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
