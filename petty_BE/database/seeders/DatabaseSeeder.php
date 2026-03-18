<?php

namespace Database\Seeders;

use App\Models\KiemKe;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            PhanQuyenSeeder::class, // Phải chạy trước để tạo dữ liệu quyền
            // KhachHangSeeder::class,
            ThuCungSeeder::class,
            DichVuSeeder::class,
            AdminSeeder::class,
            KhoaSeeder::class,
            DanhMucDichVuSeeder::class,
            NhanVienSeeder::class,
            LichLamViecSeeder::class,
            PhanLoaiBaiVietSeeder::class,
            BaiVietSeeder::class,
            HangHoaSeeder::class,
            NhaCungCap::class,
            PhieuNhapKhoSeeder::class,
            KiemKeSeeder::class,
            AssignDefaultRolesSeeder::class, // Chạy cuối cùng để gán quyền
            PhieuKhamSeeder::class,
        ]);
    }
}
