<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KhachHang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use function Symfony\Component\Clock\now;

class KhachHangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Xóa dữ liệu cũ và reset auto increment
        DB::table('khach_hangs')->truncate();

        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('khach_hangs')->insert(
            [
                [
                    'ho_lot' => 'Nguyễn Văn',
                    'ten' => 'An',
                    'email' => 'nguyenvan.an@example.com',
                    'mat_khau' => Hash::make('Password123!'),
                    'so_dien_thoai' => '0912345678',
                    'dia_chi' => '123 Đường A, Quận 1, TP.HCM',
                    'anh_dai_dien' => 'avatars/an.jpg',
                    'trang_thai' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'ho_lot' => 'Trần Thị',
                    'ten' => 'Bình',
                    'email' => 'tranthi.binh@example.com',
                    'mat_khau' => Hash::make('Password123!'),
                    'so_dien_thoai' => '0987654321',
                    'dia_chi' => '45 Phố B, Quận 3, TP.HCM',
                    'anh_dai_dien' => 'avatars/binh.jpg',
                    'trang_thai' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'ho_lot' => 'Lê',
                    'ten' => 'Cường',
                    'email' => 'le.cuong@example.com',
                    'mat_khau' => Hash::make('Password123!'),
                    'so_dien_thoai' => '0933123456',
                    'dia_chi' => '89 Đường C, Quận 5, TP.HCM',
                    'anh_dai_dien' => 'avatars/cuong.jpg',
                    'trang_thai' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'ho_lot' => 'Phạm',
                    'ten' => 'Dương',
                    'email' => 'pham.duong@example.com',
                    'mat_khau' => Hash::make('Password123!'),
                    'so_dien_thoai' => '0909123456',
                    'dia_chi' => '12 Ngõ D, Hà Nội',
                    'anh_dai_dien' => 'avatars/duong.jpg',
                    'trang_thai' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'ho_lot' => 'Hoàng',
                    'ten' => 'Em',
                    'email' => 'hoang.em@example.com',
                    'mat_khau' => Hash::make('Password123!'),
                    'so_dien_thoai' => '0911222333',
                    'dia_chi' => '77 Đại lộ E, Đà Nẵng',
                    'anh_dai_dien' => 'avatars/em.jpg',
                    'trang_thai' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'ho_lot' => 'Vũ',
                    'ten' => 'Phúc',
                    'email' => 'vu.phuc@example.com',
                    'mat_khau' => Hash::make('Password123!'),
                    'so_dien_thoai' => '0944556677',
                    'dia_chi' => '33 Khu F, Hải Phòng',
                    'anh_dai_dien' => 'avatars/phuc.jpg',
                    'trang_thai' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'ho_lot' => 'Đỗ',
                    'ten' => 'Giang',
                    'email' => 'do.giang@example.com',
                    'mat_khau' => Hash::make('Password123!'),
                    'so_dien_thoai' => '0955667788',
                    'dia_chi' => '55 Phố G, Cần Thơ',
                    'anh_dai_dien' => 'avatars/giang.jpg',
                    'trang_thai' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'ho_lot' => 'Bùi',
                    'ten' => 'Hà',
                    'email' => 'bui.ha@example.com',
                    'mat_khau' => Hash::make('Password123!'),
                    'so_dien_thoai' => '0966778899',
                    'dia_chi' => '101 Đường H, Nha Trang',
                    'anh_dai_dien' => 'avatars/ha.jpg',
                    'trang_thai' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'ho_lot' => 'Ngô',
                    'ten' => 'Minh',
                    'email' => 'ngo.minh@example.com',
                    'mat_khau' => Hash::make('Password123!'),
                    'so_dien_thoai' => '0977889900',
                    'dia_chi' => '200 Khu I, Vũng Tàu',
                    'anh_dai_dien' => 'avatars/minh.jpg',
                    'trang_thai' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'ho_lot' => 'Dương',
                    'ten' => 'Lan',
                    'email' => 'duong.lan@example.com',
                    'mat_khau' => Hash::make('Password123!'),
                    'so_dien_thoai' => '0988990011',
                    'dia_chi' => '9 Hẻm J, Huế',
                    'anh_dai_dien' => 'avatars/lan.jpg',
                    'trang_thai' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]
        );
    }
}
