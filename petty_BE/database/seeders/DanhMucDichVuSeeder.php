<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DanhMucDichVuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Xóa dữ liệu cũ và reset auto increment
        DB::table('danh_muc_dich_vus')->truncate();

        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = now();
        DB::table('danh_muc_dich_vus')->insert([
            [
                'ten_nhom' => 'Khám tổng quát',
                'mo_ta' => 'Khám và đánh giá toàn diện sức khỏe thú cưng',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten_nhom' => 'Tiêm phòng',
                'mo_ta' => 'Các loại vắc-xin phòng bệnh theo lịch',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten_nhom' => 'Cắt tỉa & Vệ sinh',
                'mo_ta' => 'Dịch vụ tỉa lông, cắt móng và tắm gội',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten_nhom' => 'Khám nha khoa',
                'mo_ta' => 'Chăm sóc răng miệng, lấy cao răng và điều trị nhiễm trùng',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten_nhom' => 'Siêu âm & Cận lâm sàng',
                'mo_ta' => 'Siêu âm, xét nghiệm máu và chẩn đoán hình ảnh',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten_nhom' => 'Phẫu thuật',
                'mo_ta' => 'Phẫu thuật nhỏ và lớn, vô cảm và chăm sóc hậu phẫu',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten_nhom' => 'Điều trị ký sinh trùng',
                'mo_ta' => 'Điều trị và phòng ngừa ve, rận và giun sán',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten_nhom' => 'Dinh dưỡng & Tư vấn',
                'mo_ta' => 'Tư vấn chế độ ăn, cân nặng và dinh dưỡng đặc biệt',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten_nhom' => 'Khám bệnh cấp cứu',
                'mo_ta' => 'Tiếp nhận và xử trí các trường hợp cấp cứu',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten_nhom' => 'Thẩm mỹ & Phục hồi',
                'mo_ta' => 'Dịch vụ thẩm mỹ, phục hồi chức năng và vật lý trị liệu',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
