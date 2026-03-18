<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PhieuChiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Xóa dữ liệu cũ
        DB::table('phieu_chis')->delete();
        DB::table('phieu_chis')->truncate();

        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert dữ liệu mẫu
        DB::table('phieu_chis')->insert([
            // Phiếu chi nhập hàng - Đã hoàn thành
            [
                'ma_phieu_chi' => 'PC001',
                'loai_phieu_chi' => 'chi_nhap_hang',
                'ly_do_chi' => 'Thanh toán nhập thuốc thú y từ Bayer',
                'tong_so_tien' => 50000000,
                'so_tien_thanh_toan_ngay' => 50000000,
                'so_tien_con_no' => 0,
                'tien_mat' => 20000000,
                'tien_chuyen_khoan' => 30000000,
                'anh_chung_tu' => json_encode(['chungtu_001_1.jpg', 'chungtu_001_2.jpg']),
                'doi_tuong_nhan_tien' => null,
                'nha_cung_cap_id' => 1,
                'trang_thai' => 'da_hoan_thanh',
                'admin_id' => 1,
                'nhan_vien_id' => null,
                'nguoi_tao_type' => 'Admin',
                'ngay_chi' => Carbon::now()->subDays(25),
                'ghi_chu' => 'Thanh toán đầy đủ ngay khi nhập hàng',
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(25),
            ],

            // Phiếu chi nhập hàng - Còn nợ
            [
                'ma_phieu_chi' => 'PC002',
                'loai_phieu_chi' => 'chi_nhap_hang',
                'ly_do_chi' => 'Nhập thức ăn cho thú cưng từ Công ty Thức ăn Gia súc',
                'tong_so_tien' => 80000000,
                'so_tien_thanh_toan_ngay' => 40000000,
                'so_tien_con_no' => 40000000,
                'tien_mat' => 10000000,
                'tien_chuyen_khoan' => 30000000,
                'anh_chung_tu' => json_encode(['chungtu_002.jpg']),
                'doi_tuong_nhan_tien' => null,
                'nha_cung_cap_id' => 2,
                'trang_thai' => 'con_no',
                'admin_id' => 1,
                'nhan_vien_id' => null,
                'nguoi_tao_type' => 'Admin',
                'ngay_chi' => Carbon::now()->subDays(20),
                'ghi_chu' => 'Trả trước 50%, còn nợ 40 triệu, hẹn thanh toán cuối tháng',
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(20),
            ],

            // Phiếu chi vận hành - Tiền lương
            [
                'ma_phieu_chi' => 'PC003',
                'loai_phieu_chi' => 'chi_van_hanh',
                'ly_do_chi' => 'Chi trả lương tháng 11/2025 cho nhân viên',
                'tong_so_tien' => 150000000,
                'so_tien_thanh_toan_ngay' => 150000000,
                'so_tien_con_no' => 0,
                'tien_mat' => 50000000,
                'tien_chuyen_khoan' => 100000000,
                'anh_chung_tu' => json_encode(['bangluong_11_2025.pdf', 'bienbangiao_11.jpg']),
                'doi_tuong_nhan_tien' => 'Toàn thể nhân viên phòng khám',
                'nha_cung_cap_id' => null,
                'trang_thai' => 'da_hoan_thanh',
                'admin_id' => 1,
                'nhan_vien_id' => null,
                'nguoi_tao_type' => 'Admin',
                'ngay_chi' => Carbon::now()->subDays(15),
                'ghi_chu' => 'Đã chi trả đầy đủ lương tháng 11',
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(15),
            ],

            // Thêm 7 phiếu chi nữa tương tự...
            [
                'ma_phieu_chi' => 'PC004',
                'loai_phieu_chi' => 'chi_van_hanh',
                'ly_do_chi' => 'Thanh toán tiền điện nước tháng 11/2025',
                'tong_so_tien' => 8500000,
                'so_tien_thanh_toan_ngay' => 8500000,
                'so_tien_con_no' => 0,
                'tien_mat' => 8500000,
                'tien_chuyen_khoan' => 0,
                'anh_chung_tu' => json_encode(['hoadon_dien.jpg', 'hoadon_nuoc.jpg']),
                'doi_tuong_nhan_tien' => 'Công ty Điện lực - Công ty Cấp nước',
                'nha_cung_cap_id' => null,
                'trang_thai' => 'da_hoan_thanh',
                'admin_id' => 1,
                'nhan_vien_id' => null,
                'nguoi_tao_type' => 'Admin',
                'ngay_chi' => Carbon::now()->subDays(12),
                'ghi_chu' => 'Thanh toán hóa đơn tiền điện và nước',
                'created_at' => Carbon::now()->subDays(12),
                'updated_at' => Carbon::now()->subDays(12),
            ],
        ]);

        echo "✓ Đã seed dữ liệu phiếu chi thành công!\n";
    }
}
