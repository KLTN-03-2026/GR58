<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PhanLoaiBaiViet;
use Illuminate\Support\Facades\DB;

class PhanLoaiBaiVietSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Xóa dữ liệu cũ và reset auto increment
        DB::table('phan_loai_bai_viets')->truncate();

        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
            [
                'ten_phan_loai' => 'Chăm sóc thú cưng',
                'slug' => 'cham-soc-thu-cung',
                'mo_ta' => 'Các bài viết về cách chăm sóc, vệ sinh và dinh dưỡng cho thú cưng.',
            ],
            [
                'ten_phan_loai' => 'Sức khỏe thú cưng',
                'slug' => 'suc-khoe-thu-cung',
                'mo_ta' => 'Các bài viết về sức khỏe, bệnh tật và cách phòng ngừa bệnh cho thú cưng.',
            ],
            [
                'ten_phan_loai' => 'Huấn luyện và lựa chọn thức ăn',
                'slug' => 'huan-luyen-va-lua-chon-thuc-an',
                'mo_ta' => 'Các bài viết về huấn luyện thú cưng và lựa chọn thức ăn phù hợp.',
            ],
        ];

        foreach ($categories as $category) {
            PhanLoaiBaiViet::create($category);
        }
    }
}
