<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PhieuNhapKhoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Xóa dữ liệu cũ và reset auto increment
        DB::table('chi_tiet_phieu_nhap_khos')->truncate();
        DB::table('phieu_nhap_khos')->truncate();
        DB::table('phieu_chis')->truncate();

        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Tạo 10 phiếu chi trước
        $phieuChiIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $tongTien = rand(5000000, 50000000);
            $soTienThanhToanNgay = rand(0, 1) ? $tongTien : rand(0, $tongTien);
            $phieuChiIds[] = DB::table('phieu_chis')->insertGetId([
                'ma_phieu_chi' => 'PC' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'loai_phieu_chi' => 'chi_nhap_hang',
                'ly_do_chi' => 'Thanh toán tiền hàng nhập kho đợt ' . $i,
                'tong_so_tien' => $tongTien,
                'so_tien_thanh_toan_ngay' => $soTienThanhToanNgay,
                'so_tien_con_no' => $tongTien - $soTienThanhToanNgay,
                'tien_mat' => rand(0, $soTienThanhToanNgay),
                'tien_chuyen_khoan' => $soTienThanhToanNgay - rand(0, $soTienThanhToanNgay),
                'anh_chung_tu' => json_encode(['chungtu_' . $i . '.jpg']),
                'doi_tuong_nhan_tien' => null,
                'nha_cung_cap_id' => rand(1, 10),
                'trang_thai' => ($tongTien - $soTienThanhToanNgay) > 0 ? 'con_no' : 'da_hoan_thanh',
                'admin_id' => 1,
                'nhan_vien_id' => null,
                'nguoi_tao_type' => 'Admin',
                'ngay_chi' => Carbon::now()->subDays(rand(1, 30))->format('Y-m-d'),
                'ghi_chu' => 'Phiếu chi cho phiếu nhập kho đợt ' . $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Tạo 10 phiếu nhập kho
        $phieuNhapKhoData = [];
        for ($i = 1; $i <= 10; $i++) {
            $tongTien = rand(5000000, 50000000);
            $phieuNhapKhoData[] = [
                'id' => $i,
                'ma_phieu_nhap' => 'PNK' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'ngay_nhap' => Carbon::now()->subDays(rand(1, 30))->format('Y-m-d'),
                'tong_tien' => $tongTien,
                'ghi_chu' => 'Phiếu nhập kho hàng hóa đợt ' . $i,
                'trang_thai' => ['cho_duyet', 'da_duyet', 'huy'][rand(0, 2)],
                'nha_cung_cap_id' => rand(1, 10),
                'phieu_chi_id' => $phieuChiIds[$i - 1],
                'nhan_vien_id' => rand(1, 10),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('phieu_nhap_khos')->insert($phieuNhapKhoData);

        // Tạo chi tiết phiếu nhập kho (mỗi phiếu có 2-5 mặt hàng)
        $chiTietData = [];
        for ($phieuId = 1; $phieuId <= 10; $phieuId++) {
            $soMatHang = rand(2, 5);
            for ($j = 0; $j < $soMatHang; $j++) {
                $soLuong = rand(10, 100);
                $donGia = rand(50000, 500000);
                $thanhTien = $soLuong * $donGia;

                $chiTietData[] = [
                    'phieu_nhap_kho_id' => $phieuId,
                    'hang_hoa_id' => rand(1, 10),
                    'so_luong' => $soLuong,
                    'so_lo' => 'LO' . date('Ymd') . rand(100, 999),
                    'han_su_dung' => Carbon::now()->addMonths(rand(6, 36))->format('Y-m-d'),
                    'don_gia' => $donGia,
                    'thanh_tien' => $thanhTien,
                    'ghi_chu' => 'Hàng nhập mới, chất lượng tốt',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('chi_tiet_phieu_nhap_khos')->insert($chiTietData);

        // Cập nhật lại tổng tiền cho các phiếu nhập kho và phiếu chi
        foreach ($phieuNhapKhoData as $phieu) {
            $tongTienThucTe = DB::table('chi_tiet_phieu_nhap_khos')
                ->where('phieu_nhap_kho_id', $phieu['id'])
                ->sum('thanh_tien');

            // Cập nhật tổng tiền phiếu nhập kho
            DB::table('phieu_nhap_khos')
                ->where('id', $phieu['id'])
                ->update(['tong_tien' => $tongTienThucTe]);

            // Cập nhật lại số tiền phiếu chi với cấu trúc mới
            $soTienThanhToanNgay = rand(0, 1) ? $tongTienThucTe : rand(0, $tongTienThucTe);
            $soTienConNo = $tongTienThucTe - $soTienThanhToanNgay;

            DB::table('phieu_chis')
                ->where('id', $phieu['phieu_chi_id'])
                ->update([
                    'tong_so_tien' => $tongTienThucTe,
                    'so_tien_thanh_toan_ngay' => $soTienThanhToanNgay,
                    'so_tien_con_no' => $soTienConNo,
                    'tien_mat' => rand(0, $soTienThanhToanNgay),
                    'tien_chuyen_khoan' => $soTienThanhToanNgay,
                    'trang_thai' => $soTienConNo > 0 ? 'con_no' : 'da_hoan_thanh',
                ]);
        }
    }
}
