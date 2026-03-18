<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\NhanVien;
use App\Models\PhanQuyen;

class AssignDefaultRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy vai trò Admin
        $adminRole = PhanQuyen::where('ma_vai_tro', 'admin')->first();

        // Gán vai trò Admin cho tất cả admin
        if ($adminRole) {
            Admin::whereNull('phan_quyen_id')->update(['phan_quyen_id' => $adminRole->id]);
            $this->command->info('✓ Đã gán vai trò Admin cho các tài khoản admin');
        }

        // Gán vai trò mặc định cho nhân viên dựa trên vai_tro hiện tại
        $bacSiRole = PhanQuyen::where('ma_vai_tro', 'bac_si')->first();
        $dieuDuongRole = PhanQuyen::where('ma_vai_tro', 'dieu_duong')->first();
        $leTanRole = PhanQuyen::where('ma_vai_tro', 'le_tan')->first();
        $troLyRole = PhanQuyen::where('ma_vai_tro', 'tro_ly')->first();

        // Gán vai trò cho bác sĩ
        if ($bacSiRole) {
            NhanVien::where('vai_tro', 'bac_si')
                ->whereNull('phan_quyen_id')
                ->update(['phan_quyen_id' => $bacSiRole->id]);
            $this->command->info('✓ Đã gán vai trò Bác sĩ cho các nhân viên bác sĩ');
        }

        // Gán vai trò cho điều dưỡng
        if ($dieuDuongRole) {
            NhanVien::where('vai_tro', 'dieu_duong')
                ->whereNull('phan_quyen_id')
                ->update(['phan_quyen_id' => $dieuDuongRole->id]);
            $this->command->info('✓ Đã gán vai trò Điều dưỡng cho các nhân viên điều dưỡng');
        }

        // Gán vai trò cho lễ tân
        if ($leTanRole) {
            NhanVien::where('vai_tro', 'le_tan')
                ->whereNull('phan_quyen_id')
                ->update(['phan_quyen_id' => $leTanRole->id]);
            $this->command->info('✓ Đã gán vai trò Lễ tân cho các nhân viên lễ tân');
        }

        // Gán vai trò cho trợ lý
        if ($troLyRole) {
            NhanVien::where('vai_tro', 'tro_ly')
                ->whereNull('phan_quyen_id')
                ->update(['phan_quyen_id' => $troLyRole->id]);
            $this->command->info('✓ Đã gán vai trò Trợ lý cho các nhân viên trợ lý');
        }
    }
}
