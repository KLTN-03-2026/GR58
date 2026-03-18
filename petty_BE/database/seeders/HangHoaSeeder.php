<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HangHoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Xóa dữ liệu cũ và reset auto increment
        DB::table('hang_hoas')->truncate();
        DB::table('danh_muc_hang_hoas')->truncate();

        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Tạo danh mục hàng hóa trước
        $danhMuc1 = DB::table('danh_muc_hang_hoas')->insertGetId([
            'ten_danh_muc_hang_hoa' => 'Thuốc',
            'mo_ta' => 'Các loại thuốc điều trị',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $danhMuc2 = DB::table('danh_muc_hang_hoas')->insertGetId([
            'ten_danh_muc_hang_hoa' => 'Vật tư y tế',
            'mo_ta' => 'Các loại vật tư, dụng cụ y tế',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('hang_hoas')->insert([
            [
                'ma_hang_hoa' => 'HH001',
                'ten_mat_hang' => 'Thuốc kháng sinh Amoxicillin',
                'don_vi_tinh' => 'Lọ',
                'gia_von' => 50000.00,
                'gia_ban' => 75000.00,
                'dinh_muc_toi_thieu' => 10,
                'anh_san_pham' => 'products/amoxicillin.jpg',
                'tinh_trang' => 'hoat_dong',
                'danh_muc_hang_hoa_id' => $danhMuc1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_hang_hoa' => 'HH002',
                'ten_mat_hang' => 'Thuốc hạ sốt Paracetamol',
                'don_vi_tinh' => 'Vỉ',
                'gia_von' => 8000.00,
                'gia_ban' => 12000.00,
                'dinh_muc_toi_thieu' => 20,
                'anh_san_pham' => 'products/paracetamol.jpg',
                'tinh_trang' => 'hoat_dong',
                'danh_muc_hang_hoa_id' => $danhMuc1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_hang_hoa' => 'HH003',
                'ten_mat_hang' => 'Băng cứu vãn y tế',
                'don_vi_tinh' => 'Cuộn',
                'gia_von' => 15000.00,
                'gia_ban' => 25000.00,
                'dinh_muc_toi_thieu' => 15,
                'anh_san_pham' => 'products/bandage.jpg',
                'tinh_trang' => 'hoat_dong',
                'danh_muc_hang_hoa_id' => $danhMuc2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_hang_hoa' => 'HH004',
                'ten_mat_hang' => 'Nước muối sinh lý',
                'don_vi_tinh' => 'Chai',
                'gia_von' => 12000.00,
                'gia_ban' => 18000.00,
                'dinh_muc_toi_thieu' => 25,
                'anh_san_pham' => 'products/saline.jpg',
                'tinh_trang' => 'hoat_dong',
                'danh_muc_hang_hoa_id' => $danhMuc2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_hang_hoa' => 'HH005',
                'ten_mat_hang' => 'Kháng sinh tịnh mạch Ceftriaxone',
                'don_vi_tinh' => 'Chai',
                'gia_von' => 80000.00,
                'gia_ban' => 120000.00,
                'dinh_muc_toi_thieu' => 5,
                'anh_san_pham' => 'products/ceftriaxone.jpg',
                'tinh_trang' => 'hoat_dong',
                'danh_muc_hang_hoa_id' => $danhMuc1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_hang_hoa' => 'HH006',
                'ten_mat_hang' => 'Kem mỡ vết thương',
                'don_vi_tinh' => 'Tuýp',
                'gia_von' => 20000.00,
                'gia_ban' => 35000.00,
                'dinh_muc_toi_thieu' => 12,
                'anh_san_pham' => 'products/wound_cream.jpg',
                'tinh_trang' => 'hoat_dong',
                'danh_muc_hang_hoa_id' => $danhMuc2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_hang_hoa' => 'HH007',
                'ten_mat_hang' => 'Vitamin B12 tiêm',
                'don_vi_tinh' => 'Lọ',
                'gia_von' => 35000.00,
                'gia_ban' => 55000.00,
                'dinh_muc_toi_thieu' => 8,
                'anh_san_pham' => 'products/vitamin_b12.jpg',
                'tinh_trang' => 'hoat_dong',
                'danh_muc_hang_hoa_id' => $danhMuc1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_hang_hoa' => 'HH008',
                'ten_mat_hang' => 'Khẩu trang y tế 3 lớp',
                'don_vi_tinh' => 'Hộp',
                'gia_von' => 45000.00,
                'gia_ban' => 65000.00,
                'dinh_muc_toi_thieu' => 30,
                'anh_san_pham' => 'products/mask.jpg',
                'tinh_trang' => 'hoat_dong',
                'danh_muc_hang_hoa_id' => $danhMuc2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_hang_hoa' => 'HH009',
                'ten_mat_hang' => 'Bông y tế vô trùng',
                'don_vi_tinh' => 'Gói',
                'gia_von' => 5000.00,
                'gia_ban' => 8000.00,
                'dinh_muc_toi_thieu' => 50,
                'anh_san_pham' => 'products/cotton.jpg',
                'tinh_trang' => 'hoat_dong',
                'danh_muc_hang_hoa_id' => $danhMuc2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_hang_hoa' => 'HH010',
                'ten_mat_hang' => 'Thuốc hạ huyết áp Lisinopril',
                'don_vi_tinh' => 'Vỉ',
                'gia_von' => 25000.00,
                'gia_ban' => 40000.00,
                'dinh_muc_toi_thieu' => 15,
                'anh_san_pham' => 'products/lisinopril.jpg',
                'tinh_trang' => 'hoat_dong',
                'danh_muc_hang_hoa_id' => $danhMuc1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
