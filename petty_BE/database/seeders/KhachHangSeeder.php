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
                    'full_name' => 'Nguyễn Văn An',
                    'email' => 'nguyenvan.an@example.com',
                    'password' => Hash::make('Password123!'),
                    'phone' => '0912345678',
                    'address' => '123 Đường A, Quận 1, TP.HCM',
                    'anh_dai_dien' => 'avatars/an.jpg',
                    'trang_thai' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'full_name' => 'Trần Thị Bình',
                    'email' => 'tranthi.binh@example.com',
                    'password' => Hash::make('Password123!'),
                    'phone' => '0987654321',
                    'address' => '45 Phố B, Quận 3, TP.HCM',
                    'anh_dai_dien' => 'avatars/binh.jpg',
                    'trang_thai' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'full_name' => 'Lê Cường',
                    'email' => 'le.cuong@example.com',
                    'password' => Hash::make('Password123!'),
                    'phone' => '0933123456',
                    'address' => '89 Đường C, Quận 5, TP.HCM',
                    'anh_dai_dien' => 'avatars/cuong.jpg',
                    'trang_thai' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'full_name' => 'Phạm Dương',
                    'email' => 'pham.duong@example.com',
                    'password' => Hash::make('Password123!'),
                    'phone' => '0909123456',
                    'address' => '12 Ngõ D, Hà Nội',
                    'anh_dai_dien' => 'avatars/duong.jpg',
                    'trang_thai' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'full_name' => 'Hoàng Em',
                    'email' => 'hoang.em@example.com',
                    'password' => Hash::make('Password123!'),
                    'phone' => '0911222333',
                    'address' => '77 Đại lộ E, Đà Nẵng',
                    'anh_dai_dien' => 'avatars/em.jpg',
                    'trang_thai' => 'inactive',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'full_name' => 'Vũ Phúc',
                    'email' => 'vu.phuc@example.com',
                    'password' => Hash::make('Password123!'),
                    'phone' => '0944556677',
                    'address' => '33 Khu F, Hải Phòng',
                    'anh_dai_dien' => 'avatars/phuc.jpg',
                    'trang_thai' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'full_name' => 'Đỗ Giang',
                    'email' => 'do.giang@example.com',
                    'password' => Hash::make('Password123!'),
                    'phone' => '0955667788',
                    'address' => '55 Phố G, Cần Thơ',
                    'anh_dai_dien' => 'avatars/giang.jpg',
                    'trang_thai' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'full_name' => 'Bùi Hà',
                    'email' => 'bui.ha@example.com',
                    'password' => Hash::make('Password123!'),
                    'phone' => '0966778899',
                    'address' => '101 Đường H, Nha Trang',
                    'anh_dai_dien' => 'avatars/ha.jpg',
                    'trang_thai' => 'inactive',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'full_name' => 'Ngô Minh',
                    'email' => 'ngo.minh@example.com',
                    'password' => Hash::make('Password123!'),
                    'phone' => '0977889900',
                    'address' => '200 Khu I, Vũng Tàu',
                    'anh_dai_dien' => 'avatars/minh.jpg',
                    'trang_thai' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'full_name' => 'Dương Lan',
                    'email' => 'duong.lan@example.com',
                    'password' => Hash::make('Password123!'),
                    'phone' => '0988990011',
                    'address' => '9 Hẻm J, Huế',
                    'anh_dai_dien' => 'avatars/lan.jpg',
                    'trang_thai' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]
        );
    }
}
