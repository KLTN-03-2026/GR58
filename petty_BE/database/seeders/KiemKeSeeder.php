<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KiemKeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Xóa dữ liệu cũ bằng delete() - xóa từng bản ghi
        DB::table('kiem_kes')->delete();

        // Hoặc dùng truncate() để xóa toàn bộ và reset auto increment
        DB::table('kiem_kes')->truncate();

        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert 10 dữ liệu mẫu
        DB::table('kiem_kes')->insert([
            [
                'hang_hoa_id' => 1,
                'chi_tiet_phieu_nhap_kho_id' => 1,
                'so_luong_he_thong' => 100,
                'so_luong_thuc_te' => 95,
                'chenh_lech' => -5,
                'ly_do' => 'Phát hiện 5 lọ thuốc bị vỡ trong quá trình vận chuyển',
                'ngay_kiem_ke' => Carbon::now()->subDays(30),
                'admin_id' => 1,
                'nhan_vien_id' => null,
                'nguoi_kiem_ke_type' => 'Admin',
                'ghi_chu' => 'Đã báo cáo bộ phận kho',
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(30),
            ],
            [
                'hang_hoa_id' => 2,
                'chi_tiet_phieu_nhap_kho_id' => 2,
                'so_luong_he_thong' => 200,
                'so_luong_thuc_te' => 200,
                'chenh_lech' => 0,
                'ly_do' => null,
                'ngay_kiem_ke' => Carbon::now()->subDays(28),
                'admin_id' => null,
                'nhan_vien_id' => 1,
                'nguoi_kiem_ke_type' => 'NhanVien',
                'ghi_chu' => 'Số lượng chính xác',
                'created_at' => Carbon::now()->subDays(28),
                'updated_at' => Carbon::now()->subDays(28),
            ],
            [
                'hang_hoa_id' => 3,
                'chi_tiet_phieu_nhap_kho_id' => 3,
                'so_luong_he_thong' => 150,
                'so_luong_thuc_te' => 148,
                'chenh_lech' => -2,
                'ly_do' => 'Hai hộp thức ăn bị hết hạn sử dụng, đã loại bỏ',
                'ngay_kiem_ke' => Carbon::now()->subDays(25),
                'admin_id' => 1,
                'nhan_vien_id' => null,
                'nguoi_kiem_ke_type' => 'Admin',
                'ghi_chu' => 'Cần kiểm tra kỹ hạn sử dụng khi nhập kho',
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(25),
            ],
            [
                'hang_hoa_id' => 4,
                'chi_tiet_phieu_nhap_kho_id' => 4,
                'so_luong_he_thong' => 80,
                'so_luong_thuc_te' => 85,
                'chenh_lech' => 5,
                'ly_do' => 'Phát hiện thêm 5 lọ vaccine trong kho lạnh phụ chưa được cập nhật',
                'ngay_kiem_ke' => Carbon::now()->subDays(22),
                'admin_id' => null,
                'nhan_vien_id' => 2,
                'nguoi_kiem_ke_type' => 'NhanVien',
                'ghi_chu' => 'Đã cập nhật vào hệ thống',
                'created_at' => Carbon::now()->subDays(22),
                'updated_at' => Carbon::now()->subDays(22),
            ],
            [
                'hang_hoa_id' => 5,
                'chi_tiet_phieu_nhap_kho_id' => 5,
                'so_luong_he_thong' => 300,
                'so_luong_thuc_te' => 290,
                'chenh_lech' => -10,
                'ly_do' => 'Sử dụng khẩn cấp cho ca cấp cứu chưa kịp cập nhật',
                'ngay_kiem_ke' => Carbon::now()->subDays(20),
                'admin_id' => 1,
                'nhan_vien_id' => null,
                'nguoi_kiem_ke_type' => 'Admin',
                'ghi_chu' => 'Đã yêu cầu nhân viên cập nhật đúng quy trình',
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(20),
            ],
            [
                'hang_hoa_id' => 6,
                'chi_tiet_phieu_nhap_kho_id' => 6,
                'so_luong_he_thong' => 120,
                'so_luong_thuc_te' => 120,
                'chenh_lech' => 0,
                'ly_do' => null,
                'ngay_kiem_ke' => Carbon::now()->subDays(18),
                'admin_id' => null,
                'nhan_vien_id' => 1,
                'nguoi_kiem_ke_type' => 'NhanVien',
                'ghi_chu' => 'Kiểm kê định kỳ - Không có sai lệch',
                'created_at' => Carbon::now()->subDays(18),
                'updated_at' => Carbon::now()->subDays(18),
            ],
            [
                'hang_hoa_id' => 7,
                'chi_tiet_phieu_nhap_kho_id' => 7,
                'so_luong_he_thong' => 250,
                'so_luong_thuc_te' => 252,
                'chenh_lech' => 2,
                'ly_do' => 'Nhà cung cấp giao thừa 2 gói, chưa xuất hóa đơn',
                'ngay_kiem_ke' => Carbon::now()->subDays(15),
                'admin_id' => 1,
                'nhan_vien_id' => null,
                'nguoi_kiem_ke_type' => 'Admin',
                'ghi_chu' => 'Đã liên hệ nhà cung cấp để xác nhận',
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(15),
            ],
            [
                'hang_hoa_id' => 8,
                'chi_tiet_phieu_nhap_kho_id' => 8,
                'so_luong_he_thong' => 180,
                'so_luong_thuc_te' => 175,
                'chenh_lech' => -5,
                'ly_do' => 'Sản phẩm bị hư hỏng do lưu trữ không đúng cách',
                'ngay_kiem_ke' => Carbon::now()->subDays(12),
                'admin_id' => null,
                'nhan_vien_id' => 2,
                'nguoi_kiem_ke_type' => 'NhanVien',
                'ghi_chu' => 'Cần cải thiện điều kiện bảo quản',
                'created_at' => Carbon::now()->subDays(12),
                'updated_at' => Carbon::now()->subDays(12),
            ],
            [
                'hang_hoa_id' => 9,
                'chi_tiet_phieu_nhap_kho_id' => 9,
                'so_luong_he_thong' => 90,
                'so_luong_thuc_te' => 90,
                'chenh_lech' => 0,
                'ly_do' => null,
                'ngay_kiem_ke' => Carbon::now()->subDays(10),
                'admin_id' => 1,
                'nhan_vien_id' => null,
                'nguoi_kiem_ke_type' => 'Admin',
                'ghi_chu' => 'Kiểm kê đột xuất - Kết quả chính xác',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'hang_hoa_id' => 10,
                'chi_tiet_phieu_nhap_kho_id' => 10,
                'so_luong_he_thong' => 400,
                'so_luong_thuc_te' => 387,
                'chenh_lech' => -13,
                'ly_do' => 'Phát hiện mất mát do nhập liệu sai trong đơn hàng trước',
                'ngay_kiem_ke' => Carbon::now()->subDays(5),
                'admin_id' => null,
                'nhan_vien_id' => 1,
                'nguoi_kiem_ke_type' => 'NhanVien',
                'ghi_chu' => 'Đã điều chỉnh lại số liệu trong hệ thống',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
        ]);

        echo "✓ Đã seed 10 bản ghi kiểm kê thành công!\n";
    }
}
