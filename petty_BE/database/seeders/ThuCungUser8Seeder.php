<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ThuCung;
use Carbon\Carbon;

class ThuCungUser8Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pets = [
            [
                'ten_thu_cung' => 'Milo',
                'loai_thu_cung' => 'Chó',
                'giong_thu_cung' => 'Golden Retriever',
                'tuoi_thu_cung' => Carbon::now()->subYears(3)->format('Y-m-d'),
                'gioi_tinh' => 'Đực',
                'can_nang' => '28',
                'anh_dai_dien' => 'default/dog-male.jpg',
                'khach_hang_id' => 8,
            ],
            [
                'ten_thu_cung' => 'Luna',
                'loai_thu_cung' => 'Mèo',
                'giong_thu_cung' => 'Mèo Ba Tư',
                'tuoi_thu_cung' => Carbon::now()->subYears(2)->format('Y-m-d'),
                'gioi_tinh' => 'Cái',
                'can_nang' => '4.5',
                'anh_dai_dien' => 'default/cat-female.jpg',
                'khach_hang_id' => 8,
            ],
            [
                'ten_thu_cung' => 'Max',
                'loai_thu_cung' => 'Chó',
                'giong_thu_cung' => 'Husky',
                'tuoi_thu_cung' => Carbon::now()->subYears(4)->format('Y-m-d'),
                'gioi_tinh' => 'Đực',
                'can_nang' => '25',
                'anh_dai_dien' => 'default/dog-male.jpg',
                'khach_hang_id' => 8,
            ],
            [
                'ten_thu_cung' => 'Bella',
                'loai_thu_cung' => 'Mèo',
                'giong_thu_cung' => 'Mèo Anh Lông Ngắn',
                'tuoi_thu_cung' => Carbon::now()->subMonths(8)->format('Y-m-d'),
                'gioi_tinh' => 'Cái',
                'can_nang' => '3.2',
                'anh_dai_dien' => 'default/cat-female.jpg',
                'khach_hang_id' => 8,
            ],
            [
                'ten_thu_cung' => 'Rocky',
                'loai_thu_cung' => 'Chó',
                'giong_thu_cung' => 'Corgi',
                'tuoi_thu_cung' => Carbon::now()->subYears(1)->subMonths(6)->format('Y-m-d'),
                'gioi_tinh' => 'Đực',
                'can_nang' => '12',
                'anh_dai_dien' => 'default/dog-male.jpg',
                'khach_hang_id' => 8,
            ],
        ];

        foreach ($pets as $pet) {
            ThuCung::create($pet);
        }

        $this->command->info('Created 5 pets for user ID 8');
    }
}
