<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KhoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate pivot and main tables safely by temporarily disabling foreign key checks.
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        try {
            if (Schema::hasTable('danh_muc_dich_vu_khoa')) {
                DB::table('danh_muc_dich_vu_khoa')->truncate();
            }
            if (Schema::hasTable('khoa_nhan_vien')) {
                DB::table('khoa_nhan_vien')->truncate();
            }
            DB::table('khoas')->truncate();
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $now = now();

        DB::table('khoas')->insert([
            [
                'ten_khoa' => 'Khoa Nội Tổng Quát',
                'ma_khoa' => 'KHOA001',
                'mo_ta' => 'Chuyên khám và điều trị các bệnh nội khoa cho thú cưng.',
                'chuyen_mon' => 'Nội khoa',
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten_khoa' => 'Khoa Ngoại',
                'ma_khoa' => 'KHOA002',
                'mo_ta' => 'Thực hiện các phẫu thuật và thủ thuật cho thú nuôi.',
                'chuyen_mon' => 'Ngoại khoa',
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten_khoa' => 'Khoa Răng Miệng',
                'ma_khoa' => 'KHOA003',
                'mo_ta' => 'Chăm sóc sức khỏe răng miệng, làm sạch và nhổ răng.',
                'chuyen_mon' => 'Nha khoa',
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten_khoa' => 'Khoa Da Liễu',
                'ma_khoa' => 'KHOA004',
                'mo_ta' => 'Chẩn đoán và điều trị các bệnh da cho thú cưng.',
                'chuyen_mon' => 'Da liễu',
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten_khoa' => 'Khoa Sản',
                'ma_khoa' => 'KHOA005',
                'mo_ta' => 'Chăm sóc sinh sản, đỡ đẻ và theo dõi thai sản cho thú.',
                'chuyen_mon' => 'Sản',
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten_khoa' => 'Khoa Chẩn Đoán Hình Ảnh',
                'ma_khoa' => 'KHOA006',
                'mo_ta' => 'Thực hiện siêu âm, X-quang và các xét nghiệm hình ảnh.',
                'chuyen_mon' => 'Chẩn đoán hình ảnh',
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten_khoa' => 'Khoa Cấp Cứu',
                'ma_khoa' => 'KHOA007',
                'mo_ta' => 'Xử lý các trường hợp cấp cứu và chăm sóc khẩn cấp.',
                'chuyen_mon' => 'Cấp cứu',
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten_khoa' => 'Khoa Tiêm Chủng',
                'ma_khoa' => 'KHOA008',
                'mo_ta' => 'Thực hiện các chương trình tiêm phòng và phòng bệnh.',
                'chuyen_mon' => 'Tiêm chủng',
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten_khoa' => 'Khoa Dinh Dưỡng',
                'ma_khoa' => 'KHOA009',
                'mo_ta' => 'Tư vấn dinh dưỡng và chế độ ăn cho thú cưng.',
                'chuyen_mon' => 'Dinh dưỡng',
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ten_khoa' => 'Khoa Vật Lý Trị Liệu',
                'ma_khoa' => 'KHOA010',
                'mo_ta' => 'Hỗ trợ phục hồi chức năng cho thú sau chấn thương.',
                'chuyen_mon' => 'Vật lý trị liệu',
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
