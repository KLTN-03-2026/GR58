<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KhuyenMai extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tắt foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Xóa tất cả dữ liệu cũ
        DB::table('khuyen_mai_dich_vu')->delete(); // Xóa bảng trung gian trước
        DB::table('khuyen_mais')->truncate(); // Truncate bảng chính

        // Bật lại foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = Carbon::now();

        // 1. Mã giảm giá 20% cho tất cả khách hàng
        DB::table('khuyen_mais')->insert([
            'ten_khuyen_mai' => 'Giảm 20% cho khách hàng mới',
            'mo_ta' => 'Chào mừng khách hàng mới, giảm ngay 20% cho đơn hàng đầu tiên',
            'loai_khuyen_mai' => 'ma_giam_gia',
            'ma_code' => 'WELCOME20',
            'gia_tri_don_toi_thieu' => 200000,
            'loai_khach_hang' => 'tat_ca',
            'hinh_thuc_giam' => 'phan_tram',
            'giam_toi_da' => 100000,
            'gia_tri_giam' => 20,
            'tu_ngay' => $now,
            'den_ngay' => $now->copy()->addMonths(3),
            'tong_so_luong' => 100,
            'so_luong_da_dung' => 15,
            'gioi_han_moi_khach' => 1,
            'trang_thai' => 'dang_chay',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Giảm giá cố định 50k
        DB::table('khuyen_mais')->insert([
            'ten_khuyen_mai' => 'Giảm 50K cho đơn từ 500K',
            'mo_ta' => 'Áp dụng cho tất cả dịch vụ khám và điều trị',
            'loai_khuyen_mai' => 'ma_giam_gia',
            'ma_code' => 'GIAM50K',
            'gia_tri_don_toi_thieu' => 500000,
            'loai_khach_hang' => 'tat_ca',
            'hinh_thuc_giam' => 'so_tien',
            'giam_toi_da' => null,
            'gia_tri_giam' => 50000,
            'tu_ngay' => $now,
            'den_ngay' => $now->copy()->addMonths(2),
            'tong_so_luong' => 200,
            'so_luong_da_dung' => 45,
            'gioi_han_moi_khach' => 3,
            'trang_thai' => 'dang_chay',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. Khuyến mãi VIP - Giảm 30%
        DB::table('khuyen_mais')->insert([
            'ten_khuyen_mai' => 'Ưu đãi khách hàng VIP 30%',
            'mo_ta' => 'Dành riêng cho khách hàng VIP thân thiết',
            'loai_khuyen_mai' => 'ma_giam_gia',
            'ma_code' => 'VIP30',
            'gia_tri_don_toi_thieu' => 1000000,
            'loai_khach_hang' => 'vip',
            'hinh_thuc_giam' => 'phan_tram',
            'giam_toi_da' => 500000,
            'gia_tri_giam' => 30,
            'tu_ngay' => $now,
            'den_ngay' => $now->copy()->addMonths(6),
            'tong_so_luong' => 50,
            'so_luong_da_dung' => 8,
            'gioi_han_moi_khach' => 5,
            'trang_thai' => 'dang_chay',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 4. Chương trình tự động - Giảm 15%
        DB::table('khuyen_mais')->insert([
            'ten_khuyen_mai' => 'Khuyến mãi đầu tuần',
            'mo_ta' => 'Tự động giảm 15% cho tất cả đơn hàng vào thứ 2 hàng tuần',
            'loai_khuyen_mai' => 'chuong_trinh_tu_dong',
            'ma_code' => null,
            'gia_tri_don_toi_thieu' => 300000,
            'loai_khach_hang' => 'tat_ca',
            'hinh_thuc_giam' => 'phan_tram',
            'giam_toi_da' => 200000,
            'gia_tri_giam' => 15,
            'tu_ngay' => $now,
            'den_ngay' => $now->copy()->addYear(),
            'tong_so_luong' => null,
            'so_luong_da_dung' => 0,
            'gioi_han_moi_khach' => null,
            'trang_thai' => 'dang_chay',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 5. Flash Sale - Giảm 100K
        DB::table('khuyen_mais')->insert([
            'ten_khuyen_mai' => 'Flash Sale - Giảm 100K',
            'mo_ta' => 'Chỉ trong 7 ngày - Số lượng có hạn',
            'loai_khuyen_mai' => 'ma_giam_gia',
            'ma_code' => 'FLASH100',
            'gia_tri_don_toi_thieu' => 800000,
            'loai_khach_hang' => 'tat_ca',
            'hinh_thuc_giam' => 'so_tien',
            'giam_toi_da' => null,
            'gia_tri_giam' => 100000,
            'tu_ngay' => $now,
            'den_ngay' => $now->copy()->addDays(7),
            'tong_so_luong' => 30,
            'so_luong_da_dung' => 22,
            'gioi_han_moi_khach' => 1,
            'trang_thai' => 'dang_chay',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 6. Sinh nhật khách hàng VIP
        DB::table('khuyen_mais')->insert([
            'ten_khuyen_mai' => 'Sinh nhật VIP - Giảm 40%',
            'mo_ta' => 'Chúc mừng sinh nhật khách hàng VIP, tặng voucher giảm 40%',
            'loai_khuyen_mai' => 'ma_giam_gia',
            'ma_code' => 'BDAYVIP40',
            'gia_tri_don_toi_thieu' => 500000,
            'loai_khach_hang' => 'vip',
            'hinh_thuc_giam' => 'phan_tram',
            'giam_toi_da' => 300000,
            'gia_tri_giam' => 40,
            'tu_ngay' => $now,
            'den_ngay' => $now->copy()->addMonths(1),
            'tong_so_luong' => 20,
            'so_luong_da_dung' => 3,
            'gioi_han_moi_khach' => 1,
            'trang_thai' => 'dang_chay',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 7. Cuối tuần vui vẻ
        DB::table('khuyen_mais')->insert([
            'ten_khuyen_mai' => 'Cuối tuần vui vẻ - Giảm 25%',
            'mo_ta' => 'Tự động áp dụng vào thứ 7 và Chủ nhật',
            'loai_khuyen_mai' => 'chuong_trinh_tu_dong',
            'ma_code' => null,
            'gia_tri_don_toi_thieu' => 400000,
            'loai_khach_hang' => 'tat_ca',
            'hinh_thuc_giam' => 'phan_tram',
            'giam_toi_da' => 150000,
            'gia_tri_giam' => 25,
            'tu_ngay' => $now,
            'den_ngay' => $now->copy()->addMonths(3),
            'tong_so_luong' => null,
            'so_luong_da_dung' => 0,
            'gioi_han_moi_khach' => null,
            'trang_thai' => 'dang_chay',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 8. Khuyến mãi hè - Giảm 200K
        DB::table('khuyen_mais')->insert([
            'ten_khuyen_mai' => 'Khuyến mãi hè - Giảm 200K',
            'mo_ta' => 'Chương trình khuyến mãi lớn mùa hè cho đơn hàng trên 2 triệu',
            'loai_khuyen_mai' => 'ma_giam_gia',
            'ma_code' => 'SUMMER200',
            'gia_tri_don_toi_thieu' => 2000000,
            'loai_khach_hang' => 'tat_ca',
            'hinh_thuc_giam' => 'so_tien',
            'giam_toi_da' => null,
            'gia_tri_giam' => 200000,
            'tu_ngay' => $now,
            'den_ngay' => $now->copy()->addMonths(2),
            'tong_so_luong' => 150,
            'so_luong_da_dung' => 67,
            'gioi_han_moi_khach' => 2,
            'trang_thai' => 'dang_chay',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 9. Giảm 10% - Không giới hạn
        DB::table('khuyen_mais')->insert([
            'ten_khuyen_mai' => 'Giảm 10% mọi dịch vụ',
            'mo_ta' => 'Mã giảm giá phổ biến cho tất cả khách hàng',
            'loai_khuyen_mai' => 'ma_giam_gia',
            'ma_code' => 'SAVE10',
            'gia_tri_don_toi_thieu' => 100000,
            'loai_khach_hang' => 'tat_ca',
            'hinh_thuc_giam' => 'phan_tram',
            'giam_toi_da' => 50000,
            'gia_tri_giam' => 10,
            'tu_ngay' => $now,
            'den_ngay' => $now->copy()->addMonths(12),
            'tong_so_luong' => null,
            'so_luong_da_dung' => 0,
            'gioi_han_moi_khach' => 10,
            'trang_thai' => 'dang_chay',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 10. Khuyến mãi hết hạn (đã kết thúc)
        DB::table('khuyen_mais')->insert([
            'ten_khuyen_mai' => 'Tết 2025 - Đã hết hạn',
            'mo_ta' => 'Chương trình khuyến mãi Tết đã kết thúc',
            'loai_khuyen_mai' => 'ma_giam_gia',
            'ma_code' => 'TET2025',
            'gia_tri_don_toi_thieu' => 500000,
            'loai_khach_hang' => 'tat_ca',
            'hinh_thuc_giam' => 'phan_tram',
            'giam_toi_da' => 250000,
            'gia_tri_giam' => 35,
            'tu_ngay' => $now->copy()->subMonths(2),
            'den_ngay' => $now->copy()->subMonths(1),
            'tong_so_luong' => 100,
            'so_luong_da_dung' => 100,
            'gioi_han_moi_khach' => 1,
            'trang_thai' => 'da_ket_thuc',
            'created_at' => $now->copy()->subMonths(2),
            'updated_at' => $now,
        ]);
    }
}
