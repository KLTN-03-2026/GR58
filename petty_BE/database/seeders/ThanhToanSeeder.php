<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LichHen;
use App\Models\KhuyenMai;
use Carbon\Carbon;

class ThanhToanSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('thanh_toans')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Lấy các lịch hẹn đã thanh toán
        $lichHens = LichHen::where('da_thanh_toan', true)
            ->whereNotNull('thoi_gian_thanh_toan')
            ->get();

        if ($lichHens->isEmpty()) {
            $this->command->warn('⚠️ Không có lịch hẹn đã thanh toán. Hãy chạy LichHenSeeder trước.');
            return;
        }

        // Lấy danh sách khuyến mãi (nếu có)
        $khuyenMais = KhuyenMai::where('trang_thai', 'dang_chay')
            ->where('tu_ngay', '<=', Carbon::now())
            ->where('den_ngay', '>=', Carbon::now())
            ->get();

        $thanhToanInserts = [];
        $counter = 1;

        foreach ($lichHens as $lichHen) {
            // Tính toán giảm giá (ngẫu nhiên có áp dụng khuyến mãi hay không)
            $apDungKhuyenMai = rand(0, 100) < 30; // 30% có khuyến mãi
            $khuyenMai = null;
            $soTienGiam = 0;
            $maGiamGia = null;
            $loaiGiam = 'khong_giam';

            if ($apDungKhuyenMai && $khuyenMais->isNotEmpty()) {
                $khuyenMai = $khuyenMais->random();
                $maGiamGia = $khuyenMai->ma_code;
                
                if ($khuyenMai->hinh_thuc_giam === 'phan_tram') {
                    $soTienGiam = ($lichHen->tong_tien * $khuyenMai->gia_tri_giam) / 100;
                    if ($khuyenMai->giam_toi_da && $soTienGiam > $khuyenMai->giam_toi_da) {
                        $soTienGiam = $khuyenMai->giam_toi_da;
                    }
                    $loaiGiam = 'ma_code';
                } else {
                    $soTienGiam = $khuyenMai->gia_tri_giam;
                    $loaiGiam = 'ma_code';
                }
            }

            $tongTienGoc = $lichHen->tong_tien;
            $tongTienSauGiam = $tongTienGoc - $soTienGiam;

            // Phân bổ tiền mặt và online
            $hinhThucThanhToan = $lichHen->phuong_thuc_thanh_toan ?? 'tien_mat';
            
            // Map từ giá trị cũ sang giá trị mới
            $mappingHinhThuc = [
                'cash' => 'tien_mat',
                'momo' => 'momo',
                'vnpay' => 'vnpay',
                'zalopay' => 'momo', // Zalopay map sang momo
            ];
            
            $hinhThucThanhToan = $mappingHinhThuc[$hinhThucThanhToan] ?? 'tien_mat';
            
            $tienMat = 0;
            $tienOnline = 0;
            $maGiaoDichOnline = null;

            if (in_array($hinhThucThanhToan, ['momo', 'vnpay'])) {
                $tienOnline = $tongTienSauGiam;
                $maGiaoDichOnline = strtoupper($hinhThucThanhToan) . '_' . time() . rand(1000, 9999);
            } else {
                $tienMat = $tongTienSauGiam;
            }

            $thanhToanInserts[] = [
                'ma_thanh_toan'       => 'TT' . str_pad($counter++, 6, '0', STR_PAD_LEFT),
                'lich_hen_id'         => $lichHen->id,
                'khach_hang_id'       => $lichHen->khach_hang_id,
                'tong_tien_goc'       => $tongTienGoc,
                'so_tien_giam'        => $soTienGiam,
                'tong_tien_sau_giam'  => $tongTienSauGiam,
                'hinh_thuc_thanh_toan' => $hinhThucThanhToan,
                'tien_mat'            => $tienMat,
                'tien_online'         => $tienOnline,
                'ma_giao_dich_online' => $maGiaoDichOnline,
                'khuyen_mai_id'       => $khuyenMai?->id,
                'ma_giam_gia'         => $maGiamGia,
                'loai_giam'           => $loaiGiam,
                'trang_thai'          => 'da_thanh_toan',
                'nhan_vien_id'        => $lichHen->nhan_vien_id,
                'admin_id'            => rand(0, 100) < 20 ? 1 : null, // 20% do admin xử lý
                'ngay_thanh_toan'     => $lichHen->thoi_gian_thanh_toan,
                'ghi_chu'             => $apDungKhuyenMai ? 'Đã áp dụng khuyến mãi' : null,
                'created_at'          => $lichHen->thoi_gian_thanh_toan,
                'updated_at'          => $lichHen->thoi_gian_thanh_toan,
            ];
        }

        // Insert thanh toán
        DB::table('thanh_toans')->insert($thanhToanInserts);

        $this->command->info('✅ Đã tạo ' . count($thanhToanInserts) . ' thanh toán mẫu.');
    }
}
