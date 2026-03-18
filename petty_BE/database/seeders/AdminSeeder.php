<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Xóa dữ liệu cũ và reset auto increment
        DB::table('admins')->truncate();

        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('admins')->insert([
            [
                'ho_ten' => 'Nguyen Van A',
                'anh_dai_dien' => null,
                'mat_khau' => Hash::make('123456789'),
                'email' => 'admin1@example.com',
                'so_dien_thoai' => '0912345678',
                'dia_chi' => '123 Duong ABC, Quan 1, TP.HCM',
                'trang_thai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ho_ten' => 'Tran Thi B',
                'anh_dai_dien' => null,
                'mat_khau' => Hash::make('123456789'),
                'email' => 'admin2@example.com',
                'so_dien_thoai' => '0987654321',
                'dia_chi' => '456 Duong XYZ, Quan 3, TP.HCM',
                'trang_thai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ho_ten' => 'Le Van C',
                'anh_dai_dien' => null,
                'mat_khau' => Hash::make('123456789'),
                'email' => 'admin3@example.com',
                'so_dien_thoai' => '0901122334',
                'dia_chi' => '789 Duong QWE, Quan 5, TP.HCM',
                'trang_thai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
