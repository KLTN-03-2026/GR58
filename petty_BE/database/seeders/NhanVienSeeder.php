<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NhanVien;
use Illuminate\Support\Facades\DB;

class NhanVienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Xóa dữ liệu cũ và reset auto increment
        DB::table('nhan_viens')->truncate();

        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $samples = [
            [
                'full_name' => 'Nguyễn Văn An',
                'email' => 'nv1@example.com',
                'phone' => '0901000001',
                'address' => '123 Đường A, Quận 1, TP.HCM',
                'anh_dai_dien' => null,
                'vai_tro' => 'bac_si',
                'chuc_danh' => 'Bác sĩ thú y',
                'nam_kinh_nghiem' => 8,
                'chung_chi_hanh_nghe' => 'Chứng chỉ hành nghề Thú y - A1',
                'bang_cap_chuyen_mon' => 'Cử nhân Thú y',
                'password' => 'password',
                'trang_thai' => 'hoat_dong',
            ],
            [
                'full_name' => 'Trần Thị Bình',
                'email' => 'nv2@example.com',
                'phone' => '0901000002',
                'address' => '45 Đường B, Quận 3, TP.HCM',
                'anh_dai_dien' => null,
                'vai_tro' => 'y_ta',
                'chuc_danh' => 'Y tá chuyên khoa',
                'nam_kinh_nghiem' => 4,
                'chung_chi_hanh_nghe' => 'Chứng chỉ Y tá - B',
                'bang_cap_chuyen_mon' => 'Cao đẳng Điều dưỡng',
                'password' => 'password',
                'trang_thai' => 'hoat_dong',
            ],
            [
                'full_name' => 'Lê Văn Cường',
                'email' => 'nv3@example.com',
                'phone' => '0901000003',
                'address' => '78 Đường C, Quận 5, TP.HCM',
                'anh_dai_dien' => null,
                'vai_tro' => 'bac_si',
                'chuc_danh' => 'Bác sĩ chuyên khoa II',
                'nam_kinh_nghiem' => 12,
                'chung_chi_hanh_nghe' => 'Chứng chỉ chuyên môn - C',
                'bang_cap_chuyen_mon' => 'Thạc sĩ Thú y',
                'password' => 'password',
                'trang_thai' => 'hoat_dong',
            ],
            [
                'full_name' => 'Phạm Thị Dung',
                'email' => 'nv4@example.com',
                'phone' => '0901000004',
                'address' => '9 Đường D, Quận 7, TP.HCM',
                'anh_dai_dien' => null,
                'vai_tro' => 'y_ta',
                'chuc_danh' => 'Y tá',
                'nam_kinh_nghiem' => 2,
                'chung_chi_hanh_nghe' => 'Chứng chỉ Y tá - cơ bản',
                'bang_cap_chuyen_mon' => 'Cao đẳng Điều dưỡng',
                'password' => 'password',
                'trang_thai' => 'hoat_dong',
            ],
            [
                'full_name' => 'Hoàng Minh',
                'email' => 'nv5@example.com',
                'phone' => '0901000005',
                'address' => '200 Đường E, Quận 2, TP.HCM',
                'anh_dai_dien' => null,
                'vai_tro' => 'bac_si',
                'chuc_danh' => 'Bác sĩ điều trị',
                'nam_kinh_nghiem' => 6,
                'chung_chi_hanh_nghe' => 'Chứng chỉ hành nghề - B',
                'bang_cap_chuyen_mon' => 'Cử nhân Thú y',
                'password' => 'password',
                'trang_thai' => 'hoat_dong',
            ],
            [
                'full_name' => 'Ngô Thị Hà',
                'email' => 'nv6@example.com',
                'phone' => '0901000006',
                'address' => '11 Đường F, Quận 4, TP.HCM',
                'anh_dai_dien' => null,
                'vai_tro' => 'y_ta',
                'chuc_danh' => 'Y tá phòng khám',
                'nam_kinh_nghiem' => 5,
                'chung_chi_hanh_nghe' => 'Chứng chỉ Y tá - B',
                'bang_cap_chuyen_mon' => 'Cao đẳng Điều dưỡng',
                'password' => 'password',
                'trang_thai' => 'hoat_dong',
            ],
            [
                'full_name' => 'Đặng Quang',
                'email' => 'nv7@example.com',
                'phone' => '0901000007',
                'address' => '33 Đường G, Quận 6, TP.HCM',
                'anh_dai_dien' => null,
                'vai_tro' => 'bac_si',
                'chuc_danh' => 'Bác sĩ phẫu thuật',
                'nam_kinh_nghiem' => 15,
                'chung_chi_hanh_nghe' => 'Chứng chỉ phẫu thuật chuyên sâu',
                'bang_cap_chuyen_mon' => 'Tiến sĩ Thú y',
                'password' => 'password',
                'trang_thai' => 'hoat_dong',
            ],
            [
                'full_name' => 'Vũ Thị Lan',
                'email' => 'nv8@example.com',
                'phone' => '0901000008',
                'address' => '55 Đường H, Quận 8, TP.HCM',
                'anh_dai_dien' => null,
                'vai_tro' => 'y_ta',
                'chuc_danh' => 'Y tá phục hồi',
                'nam_kinh_nghiem' => 7,
                'chung_chi_hanh_nghe' => 'Chứng chỉ phục hồi chức năng',
                'bang_cap_chuyen_mon' => 'Cử nhân Điều dưỡng',
                'password' => 'password',
                'trang_thai' => 'hoat_dong',
            ],
            [
                'full_name' => 'Trương Văn Hùng',
                'email' => 'nv9@example.com',
                'phone' => '0901000009',
                'address' => '66 Đường I, Quận 9, TP.HCM',
                'anh_dai_dien' => null,
                'vai_tro' => 'bac_si',
                'chuc_danh' => 'Bác sĩ nội khoa',
                'nam_kinh_nghiem' => 10,
                'chung_chi_hanh_nghe' => 'Chứng chỉ nội khoa',
                'bang_cap_chuyen_mon' => 'Thạc sĩ Thú y',
                'password' => 'password',
                'trang_thai' => 'hoat_dong',
            ],
            [
                'full_name' => 'Phan Thị Oanh',
                'email' => 'nv10@example.com',
                'phone' => '0901000010',
                'address' => '77 Đường J, Quận 10, TP.HCM',
                'anh_dai_dien' => null,
                'vai_tro' => 'y_ta',
                'chuc_danh' => 'Y tá trưởng',
                'nam_kinh_nghiem' => 11,
                'chung_chi_hanh_nghe' => 'Chứng chỉ quản lý điều dưỡng',
                'bang_cap_chuyen_mon' => 'Cử nhân Điều dưỡng',
                'password' => 'password',
                'trang_thai' => 'hoat_dong',
            ],
        ];

        // Use updateOrCreate to avoid FK/truncate issues and make seeder idempotent
        foreach ($samples as $item) {
            NhanVien::updateOrCreate(
                ['email' => $item['email']],
                $item
            );
        }
    }
}
