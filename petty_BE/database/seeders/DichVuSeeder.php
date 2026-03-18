<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DichVuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Xóa dữ liệu cũ và reset auto increment
        DB::table('dich_vus')->truncate();

        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = now();

        // fetch available category ids to associate some services
        $categoryIds = DB::table('danh_muc_dich_vus')->pluck('id')->toArray();

        $records = [
            [
                'ten' => 'Khám tổng quát',
                'ma_dich_vu' => 'DV001',
                'gia_tien' => 200000.00,
                'thoi_gian_thuc_hien' => 30,
                'mo_ta' => 'Khám sức khỏe tổng quát cho thú cưng',
                'huong_dan' => 'Đưa thú cưng đến phòng khám, giữ yên trong quá trình khám',
                'anh_dich_vu' => 'kham_tong_quat.jpg',
                'trang_thai' => 'kinh_doanh',
                'danh_muc_id' => $categoryIds ? $categoryIds[0] ?? null : null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten' => 'Vệ sinh tai',
                'ma_dich_vu' => 'DV002',
                'gia_tien' => 80000.00,
                'thoi_gian_thuc_hien' => 15,
                'mo_ta' => 'Làm sạch tai, kiểm tra nhiễm trùng',
                'huong_dan' => 'Không dùng tăm bông sâu vào ống tai, làm theo hướng dẫn nhân viên',
                'anh_dich_vu' => 'vesinh_tai.jpg',
                'trang_thai' => 'kinh_doanh',
                'danh_muc_id' => $categoryIds ? ($categoryIds[1] ?? $categoryIds[0]) : null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten' => 'Cắt tỉa lông',
                'ma_dich_vu' => 'DV003',
                'gia_tien' => 150000.00,
                'thoi_gian_thuc_hien' => 45,
                'mo_ta' => 'Grooming cơ bản: tắm, sấy, cắt tỉa',
                'huong_dan' => 'Đặt lịch trước, đảm bảo thú cưng đã ăn nhẹ',
                'anh_dich_vu' => 'cat_tia_long.jpg',
                'trang_thai' => 'kinh_doanh',
                'danh_muc_id' => $categoryIds ? ($categoryIds[2] ?? $categoryIds[0]) : null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten' => 'Tiêm phòng cơ bản',
                'ma_dich_vu' => 'DV004',
                'gia_tien' => 300000.00,
                'thoi_gian_thuc_hien' => 10,
                'mo_ta' => 'Tiêm phòng định kỳ (vắc-xin cơ bản)',
                'huong_dan' => 'Mang theo sổ tiêm chủng nếu có',
                'anh_dich_vu' => 'tiem_phong.jpg',
                'trang_thai' => 'kinh_doanh',
                'danh_muc_id' => $categoryIds ? ($categoryIds[3] ?? $categoryIds[0]) : null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten' => 'Khám nha khoa',
                'ma_dich_vu' => 'DV005',
                'gia_tien' => 180000.00,
                'thoi_gian_thuc_hien' => 30,
                'mo_ta' => 'Kiểm tra răng miệng, làm sạch cao răng cơ bản',
                'huong_dan' => 'Không cho ăn trước khi làm sạch nếu được yêu cầu',
                'anh_dich_vu' => 'kham_nha_khoa.jpg',
                'trang_thai' => 'kinh_doanh',
                'danh_muc_id' => $categoryIds ? ($categoryIds[4] ?? $categoryIds[0]) : null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten' => 'Phẫu thuật nhỏ',
                'ma_dich_vu' => 'DV006',
                'gia_tien' => 1200000.00,
                'thoi_gian_thuc_hien' => 120,
                'mo_ta' => 'Phẫu thuật nhỏ dưới gây mê',
                'huong_dan' => 'Nhịn ăn trước phẫu thuật theo hướng dẫn bác sĩ',
                'anh_dich_vu' => 'phau_thuat_nho.jpg',
                'trang_thai' => 'ngung',
                'danh_muc_id' => $categoryIds ? ($categoryIds[5] ?? $categoryIds[0]) : null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten' => 'Siêu âm',
                'ma_dich_vu' => 'DV007',
                'gia_tien' => 350000.00,
                'thoi_gian_thuc_hien' => 25,
                'mo_ta' => 'Siêu âm chẩn đoán nội tạng',
                'huong_dan' => 'Không ăn vài giờ trước khi siêu âm bụng',
                'anh_dich_vu' => 'sieu_am.jpg',
                'trang_thai' => 'kinh_doanh',
                'danh_muc_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten' => 'Xét nghiệm máu',
                'ma_dich_vu' => 'DV008',
                'gia_tien' => 250000.00,
                'thoi_gian_thuc_hien' => 20,
                'mo_ta' => 'Xét nghiệm công thức máu cơ bản',
                'huong_dan' => 'Không cho ăn trước khi lấy mẫu nếu cần',
                'anh_dich_vu' => 'xet_nghiem_mau.jpg',
                'trang_thai' => 'kinh_doanh',
                'danh_muc_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten' => 'Triệt sản',
                'ma_dich_vu' => 'DV009',
                'gia_tien' => 1500000.00,
                'thoi_gian_thuc_hien' => 90,
                'mo_ta' => 'Triệt sản cho chó/mèo (bao gồm tiền mê và chăm sóc cơ bản)',
                'huong_dan' => 'Chuẩn bị theo hướng dẫn bác sĩ trước phẫu thuật',
                'anh_dich_vu' => 'triet_sterilization.jpg',
                'trang_thai' => 'kinh_doanh',
                'danh_muc_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten' => 'Khám tai mũi họng',
                'ma_dich_vu' => 'DV010',
                'gia_tien' => 120000.00,
                'thoi_gian_thuc_hien' => 20,
                'mo_ta' => 'Khám chuyên khoa tai mũi họng',
                'huong_dan' => 'Đeo rọ mõm nếu cần khi khám',
                'anh_dich_vu' => 'kham_tmh.jpg',
                'trang_thai' => 'kinh_doanh',
                'danh_muc_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('dich_vus')->insert($records);
    }
}
