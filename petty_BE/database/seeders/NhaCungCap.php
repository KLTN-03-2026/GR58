<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NhaCungCap extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Xóa dữ liệu cũ và reset auto increment
        DB::table('nha_cung_caps')->truncate();

        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('nha_cung_caps')->insert([
            [
                'ma_nha_cung_cap' => 'NCC001',
                'ten_nha_cung_cap' => 'Công ty TNHH Thuốc Thú Y Bayer Việt Nam',
                'ten_nguoi_lien_he' => 'Nguyễn Văn An',
                'so_dien_thoai' => '0901234567',
                'dia_chi' => '123 Đường Lê Lợi, Quận 1, TP.HCM',
                'email' => 'contact@bayer-vet.vn',
                'ma_so_thue' => '0123456789',
                'mo_ta' => 'Cung cấp thuốc thú y, vaccine và các sản phẩm chăm sóc thú cưng chất lượng cao',
                'trang_thai' => 'hoat_dong',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_nha_cung_cap' => 'NCC002',
                'ten_nha_cung_cap' => 'Công ty Cổ phần Thức ăn Gia súc Việt Nam',
                'ten_nguoi_lien_he' => 'Trần Thị Bình',
                'so_dien_thoai' => '0912345678',
                'dia_chi' => '456 Đường Nguyễn Huệ, Quận 3, TP.HCM',
                'email' => 'sales@petfood.vn',
                'ma_so_thue' => '0234567891',
                'mo_ta' => 'Chuyên cung cấp thức ăn khô, thức ăn ướt cho chó mèo và các loại thú cưng',
                'trang_thai' => 'hoat_dong',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_nha_cung_cap' => 'NCC003',
                'ten_nha_cung_cap' => 'Công ty TNHH Thiết bị Y tế Thú y Medvet',
                'ten_nguoi_lien_he' => 'Lê Văn Cường',
                'so_dien_thoai' => '0923456789',
                'dia_chi' => '789 Đường Trần Hưng Đạo, Quận 5, TP.HCM',
                'email' => 'info@medvet.com.vn',
                'ma_so_thue' => '0345678912',
                'mo_ta' => 'Cung cấp thiết bị y tế, dụng cụ phẫu thuật và máy móc chuyên dụng cho thú y',
                'trang_thai' => 'hoat_dong',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_nha_cung_cap' => 'NCC004',
                'ten_nha_cung_cap' => 'Công ty CP Phụ kiện Thú cưng PetMart',
                'ten_nguoi_lien_he' => 'Phạm Thị Dung',
                'so_dien_thoai' => '0934567890',
                'dia_chi' => '321 Đường Cách Mạng Tháng 8, Quận 10, TP.HCM',
                'email' => 'order@petmart.vn',
                'ma_so_thue' => '0456789123',
                'mo_ta' => 'Chuyên cung cấp phụ kiện, đồ chơi, quần áo và vật dụng chăm sóc thú cưng',
                'trang_thai' => 'hoat_dong',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_nha_cung_cap' => 'NCC005',
                'ten_nha_cung_cap' => 'Công ty TNHH Vaccine Thú y VietVax',
                'ten_nguoi_lien_he' => 'Hoàng Văn Em',
                'so_dien_thoai' => '0945678901',
                'dia_chi' => '654 Đường Võ Văn Tần, Quận 3, TP.HCM',
                'email' => 'support@vietvax.com',
                'ma_so_thue' => '0567891234',
                'mo_ta' => 'Cung cấp các loại vaccine phòng bệnh cho chó, mèo và thú cưng nhỏ',
                'trang_thai' => 'hoat_dong',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_nha_cung_cap' => 'NCC006',
                'ten_nha_cung_cap' => 'Công ty CP Dụng cụ Thú y ProVet',
                'ten_nguoi_lien_he' => 'Vũ Thị Phượng',
                'so_dien_thoai' => '0956789012',
                'dia_chi' => '147 Đường Pasteur, Quận 1, TP.HCM',
                'email' => 'contact@provet.vn',
                'ma_so_thue' => '0678912345',
                'mo_ta' => 'Chuyên cung cấp dụng cụ khám bệnh, vật tư tiêu hao y tế thú y',
                'trang_thai' => 'hoat_dong',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_nha_cung_cap' => 'NCC007',
                'ten_nha_cung_cap' => 'Công ty TNHH Thức ăn Royal Canin Việt Nam',
                'ten_nguoi_lien_he' => 'Đỗ Văn Giang',
                'so_dien_thoai' => '0967890123',
                'dia_chi' => '258 Đường Điện Biên Phủ, Quận Bình Thạnh, TP.HCM',
                'email' => 'business@royalcanin.vn',
                'ma_so_thue' => '0789123456',
                'mo_ta' => 'Phân phối thức ăn dinh dưỡng cao cấp cho chó mèo các giống loài',
                'trang_thai' => 'hoat_dong',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_nha_cung_cap' => 'NCC008',
                'ten_nha_cung_cap' => 'Công ty CP Vệ sinh Thú cưng CleanPet',
                'ten_nguoi_lien_he' => 'Bùi Thị Hà',
                'so_dien_thoai' => '0978901234',
                'dia_chi' => '369 Đường Hai Bà Trưng, Quận 1, TP.HCM',
                'email' => 'info@cleanpet.vn',
                'ma_so_thue' => '0891234567',
                'mo_ta' => 'Cung cấp sản phẩm vệ sinh, khử mùi, tắm rửa cho thú cưng',
                'trang_thai' => 'hoat_dong',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_nha_cung_cap' => 'NCC009',
                'ten_nha_cung_cap' => 'Công ty TNHH Dược phẩm Thú y AnimalPharm',
                'ten_nguoi_lien_he' => 'Ngô Văn Kiên',
                'so_dien_thoai' => '0989012345',
                'dia_chi' => '741 Đường Lý Thường Kiệt, Quận 11, TP.HCM',
                'email' => 'sales@animalpharm.com.vn',
                'ma_so_thue' => '0912345678',
                'mo_ta' => 'Chuyên cung cấp thuốc kháng sinh, thuốc điều trị các bệnh thường gặp ở thú cưng',
                'trang_thai' => 'hoat_dong',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_nha_cung_cap' => 'NCC010',
                'ten_nha_cung_cap' => 'Công ty CP Dinh dưỡng Thú y NutriPet',
                'ten_nguoi_lien_he' => 'Lý Thị Lan',
                'so_dien_thoai' => '0990123456',
                'dia_chi' => '852 Đường Xô Viết Nghệ Tĩnh, Quận Bình Thạnh, TP.HCM',
                'email' => 'customer@nutripet.vn',
                'ma_so_thue' => '1023456789',
                'mo_ta' => 'Cung cấp thực phẩm chức năng, vitamin và khoáng chất bổ sung cho thú cưng',
                'trang_thai' => 'hoat_dong',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
