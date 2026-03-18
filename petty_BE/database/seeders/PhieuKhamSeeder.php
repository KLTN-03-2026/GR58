<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LichHen;
use App\Models\NhanVien;

class PhieuKhamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('phieu_khams')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Lấy dữ liệu lịch hẹn và nhân viên từ database
        $lichHens = LichHen::pluck('id')->toArray();
        $nhanViens = NhanVien::pluck('id')->toArray();

        if (empty($lichHens) || empty($nhanViens)) {
            $this->command->warn('⚠️ Chưa có dữ liệu lịch hẹn hoặc nhân viên. Bỏ qua seeder PhieuKham.');
            return;
        }

        // Lấy 10 lịch hẹn đầu tiên
        $lichHensToUse = array_slice($lichHens, 0, 10);
        $countLichHens = count($lichHensToUse);

        if ($countLichHens == 0) {
            $this->command->warn('⚠️ Không đủ lịch hẹn để tạo dữ liệu mẫu.');
            return;
        }

        // Chuẩn bị 10 dữ liệu mẫu thủ công
        $phieuKhams = [
            [
                'lich_hen_id' => $lichHensToUse[0] ?? $lichHens[0],
                'nhan_vien_id' => $nhanViens[0] ?? $nhanViens[0],
                'nhiet_do' => 36.8,
                'can_nang' => 65.5,
                'nhip_tim' => 72,
                'nhip_tho' => 16,
                'ly_do_den_kham' => 'Khám tổng quát',
                'trieu_chung' => 'Không có triệu chứng',
                'chan_doan' => 'Sức khỏe bình thường',
                'ghi_chu' => 'Khám định kỳ hàng năm',
                'loai_chi_dinh' => 'hen_tai_kham',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lich_hen_id' => $lichHensToUse[1] ?? $lichHens[0],
                'nhan_vien_id' => $nhanViens[1] ?? $nhanViens[0],
                'nhiet_do' => 37.2,
                'can_nang' => 72.3,
                'nhip_tim' => 78,
                'nhip_tho' => 18,
                'ly_do_den_kham' => 'Đau đầu',
                'trieu_chung' => 'Đau đầu từng cơn, kéo dài 2 tuần',
                'chan_doan' => 'Căng thẳng thần kinh',
                'ghi_chu' => 'Khuyên thư giãn và uống nước nhiều',
                'loai_chi_dinh' => 'don_thuoc',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lich_hen_id' => $lichHensToUse[2] ?? $lichHens[0],
                'nhan_vien_id' => $nhanViens[2] ?? $nhanViens[0],
                'nhiet_do' => 38.5,
                'can_nang' => 58.2,
                'nhip_tim' => 88,
                'nhip_tho' => 20,
                'ly_do_den_kham' => 'Sốt cao',
                'trieu_chung' => 'Sốt 39°C, mệt mỏi, mất ngủ',
                'chan_doan' => 'Viêm đường hô hấp',
                'ghi_chu' => 'Cần xét nghiệm máu',
                'loai_chi_dinh' => 'chi_dinh_can_lam_sang',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lich_hen_id' => $lichHensToUse[3] ?? $lichHens[0],
                'nhan_vien_id' => $nhanViens[3] ?? $nhanViens[0],
                'nhiet_do' => 37.0,
                'can_nang' => 80.5,
                'nhip_tim' => 76,
                'nhip_tho' => 17,
                'ly_do_den_kham' => 'Ho liên tục',
                'trieu_chung' => 'Ho khô kéo dài 3 tuần, khó thở',
                'chan_doan' => 'Viêm phế quản',
                'ghi_chu' => 'Chỉ định chụp X-quang phổi',
                'loai_chi_dinh' => 'chi_dinh_can_lam_sang',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lich_hen_id' => $lichHensToUse[4] ?? $lichHens[0],
                'nhan_vien_id' => $nhanViens[4] ?? $nhanViens[0],
                'nhiet_do' => 36.9,
                'can_nang' => 55.0,
                'nhip_tim' => 68,
                'nhip_tho' => 15,
                'ly_do_den_kham' => 'Mệt mỏi',
                'trieu_chung' => 'Mệt mỏi liên tục, giảm năng suất',
                'chan_doan' => 'Thiếu máu nhẹ',
                'ghi_chu' => 'Cần bổ sung sắt, B12',
                'loai_chi_dinh' => 'don_thuoc',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lich_hen_id' => $lichHensToUse[5] ?? $lichHens[0],
                'nhan_vien_id' => $nhanViens[0] ?? $nhanViens[0],
                'nhiet_do' => 37.1,
                'can_nang' => 70.0,
                'nhip_tim' => 74,
                'nhip_tho' => 16,
                'ly_do_den_kham' => 'Tiêu chảy',
                'trieu_chung' => 'Tiêu chảy, đau bụng, chóng mặt',
                'chan_doan' => 'Viêm đại tràng',
                'ghi_chu' => 'Tránh thức ăn cay nóng',
                'loai_chi_dinh' => 'don_thuoc',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lich_hen_id' => $lichHensToUse[6] ?? $lichHens[0],
                'nhan_vien_id' => $nhanViens[1] ?? $nhanViens[0],
                'nhiet_do' => 36.6,
                'can_nang' => 75.5,
                'nhip_tim' => 70,
                'nhip_tho' => 14,
                'ly_do_den_kham' => 'Khám tổng quát',
                'trieu_chung' => 'Không có',
                'chan_doan' => 'Sức khỏe tốt',
                'ghi_chu' => 'Tiếp tục duy trì lối sống hiện tại',
                'loai_chi_dinh' => 'hen_tai_kham',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lich_hen_id' => $lichHensToUse[7] ?? $lichHens[0],
                'nhan_vien_id' => $nhanViens[2] ?? $nhanViens[0],
                'nhiet_do' => 37.3,
                'can_nang' => 62.0,
                'nhip_tim' => 82,
                'nhip_tho' => 19,
                'ly_do_den_kham' => 'Đau đầu',
                'trieu_chung' => 'Đau đầu khu vực thái dương, buồn nôn',
                'chan_doan' => 'Đau nửa đầu',
                'ghi_chu' => 'Kê đơn thuốc giảm đau',
                'loai_chi_dinh' => 'don_thuoc',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lich_hen_id' => $lichHensToUse[8] ?? $lichHens[0],
                'nhan_vien_id' => $nhanViens[3] ?? $nhanViens[0],
                'nhiet_do' => 36.7,
                'can_nang' => 68.5,
                'nhip_tim' => 75,
                'nhip_tho' => 16,
                'ly_do_den_kham' => 'Sốt cao',
                'trieu_chung' => 'Sốt 38.5°C, ho nhẹ',
                'chan_doan' => 'Cảm cúm',
                'ghi_chu' => 'Nghỉ ngơi, uống nước ấm',
                'loai_chi_dinh' => 'don_thuoc',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lich_hen_id' => $lichHensToUse[9] ?? $lichHens[0],
                'nhan_vien_id' => $nhanViens[4] ?? $nhanViens[0],
                'nhiet_do' => 37.0,
                'can_nang' => 76.0,
                'nhip_tim' => 73,
                'nhip_tho' => 15,
                'ly_do_den_kham' => 'Khám tổng quát',
                'trieu_chung' => 'Kiểm tra sức khỏe định kỳ',
                'chan_doan' => 'Bình thường, không bất thường',
                'ghi_chu' => 'Tiêm vắc xin thêm mũi',
                'loai_chi_dinh' => 'chi_dinh_can_lam_sang',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert dữ liệu vào bảng
        DB::table('phieu_khams')->insert($phieuKhams);

        $this->command->info('✓ Đã seed 10 phiếu khám mẫu thành công!');
    }
}

