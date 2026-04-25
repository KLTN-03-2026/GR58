<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RBACSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo Roles
        $roles = [
            ['name' => 'admin', 'display_name' => 'Quản trị viên', 'description' => 'Toàn quyền quản lý hệ thống'],
            ['name' => 'bac_si', 'display_name' => 'Bác sĩ', 'description' => 'Khám chữa bệnh, quản lý hồ sơ bệnh án'],
            ['name' => 'y_ta', 'display_name' => 'Y tá', 'description' => 'Hỗ trợ khám chữa bệnh, quản lý kho thuốc'],
            ['name' => 'le_tan', 'display_name' => 'Lễ tân', 'description' => 'Tiếp nhận lịch hẹn, quản lý khách hàng'],
            ['name' => 'tro_ly', 'display_name' => 'Trợ lý', 'description' => 'Hỗ trợ công việc hành chính'],
            ['name' => 'khach_hang', 'display_name' => 'Khách hàng', 'description' => 'Người dùng thông thường'],
        ];

        foreach ($roles as $role) {
            \DB::table('roles')->insert(array_merge($role, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Tạo Permissions theo nhóm
        $permissions = [
            // Nhóm Lịch hẹn
            ['name' => 'lich_hen.xem', 'display_name' => 'Xem lịch hẹn', 'group' => 'lich_hen'],
            ['name' => 'lich_hen.tao', 'display_name' => 'Tạo lịch hẹn', 'group' => 'lich_hen'],
            ['name' => 'lich_hen.sua', 'display_name' => 'Sửa lịch hẹn', 'group' => 'lich_hen'],
            ['name' => 'lich_hen.xoa', 'display_name' => 'Xóa lịch hẹn', 'group' => 'lich_hen'],
            ['name' => 'lich_hen.xac_nhan', 'display_name' => 'Xác nhận lịch hẹn', 'group' => 'lich_hen'],

            // Nhóm Lịch làm việc
            ['name' => 'lich_lam_viec.xem', 'display_name' => 'Xem lịch làm việc', 'group' => 'lich_lam_viec'],
            ['name' => 'lich_lam_viec.tao', 'display_name' => 'Tạo lịch làm việc', 'group' => 'lich_lam_viec'],

            // Nhóm Tài chính
            ['name' => 'tai_chinh.xem_doanh_thu', 'display_name' => 'Xem doanh thu', 'group' => 'tai_chinh'],
            ['name' => 'tai_chinh.thu_tien', 'display_name' => 'Thu tiền', 'group' => 'tai_chinh'],
            ['name' => 'tai_chinh.hoan_tien', 'display_name' => 'Hoàn tiền', 'group' => 'tai_chinh'],

            // Nhóm Phiếu chi
            ['name' => 'phieu_chi.xem', 'display_name' => 'Xem phiếu chi', 'group' => 'phieu_chi'],
            ['name' => 'phieu_chi.tao', 'display_name' => 'Tạo phiếu chi', 'group' => 'phieu_chi'],
            ['name' => 'phieu_chi.sua', 'display_name' => 'Sửa phiếu chi', 'group' => 'phieu_chi'],
            ['name' => 'phieu_chi.xoa', 'display_name' => 'Xóa phiếu chi', 'group' => 'phieu_chi'],
            ['name' => 'phieu_chi.xuat_pdf', 'display_name' => 'Xuất PDF phiếu chi', 'group' => 'phieu_chi'],
            ['name' => 'phieu_chi.thanh_toan', 'display_name' => 'Thanh toán phiếu chi', 'group' => 'phieu_chi'],

            // Nhóm Hàng hóa
            ['name' => 'hang_hoa.xem', 'display_name' => 'Xem hàng hóa', 'group' => 'hang_hoa'],
            ['name' => 'hang_hoa.tao', 'display_name' => 'Tạo hàng hóa', 'group' => 'hang_hoa'],
            ['name' => 'hang_hoa.sua', 'display_name' => 'Sửa hàng hóa', 'group' => 'hang_hoa'],
            ['name' => 'hang_hoa.xoa', 'display_name' => 'Xóa hàng hóa', 'group' => 'hang_hoa'],
            ['name' => 'hang_hoa.doi_trang_thai', 'display_name' => 'Đổi trạng thái hàng hóa', 'group' => 'hang_hoa'],

            // Nhóm Danh mục hàng hóa
            ['name' => 'danh_muc_hang_hoa.xem', 'display_name' => 'Xem danh mục hàng hóa', 'group' => 'danh_muc_hang_hoa'],
            ['name' => 'danh_muc_hang_hoa.tao', 'display_name' => 'Tạo danh mục hàng hóa', 'group' => 'danh_muc_hang_hoa'],
            ['name' => 'danh_muc_hang_hoa.sua', 'display_name' => 'Sửa danh mục hàng hóa', 'group' => 'danh_muc_hang_hoa'],
            ['name' => 'danh_muc_hang_hoa.xoa', 'display_name' => 'Xóa danh mục hàng hóa', 'group' => 'danh_muc_hang_hoa'],

            // Nhóm Phiếu nhập kho
            ['name' => 'phieu_nhap_kho.xem', 'display_name' => 'Xem phiếu nhập kho', 'group' => 'phieu_nhap_kho'],
            ['name' => 'phieu_nhap_kho.tao', 'display_name' => 'Tạo phiếu nhập kho', 'group' => 'phieu_nhap_kho'],
            ['name' => 'phieu_nhap_kho.doi_trang_thai', 'display_name' => 'Đổi trạng thái phiếu nhập kho', 'group' => 'phieu_nhap_kho'],
            ['name' => 'phieu_nhap_kho.xuat_pdf', 'display_name' => 'Xuất PDF phiếu nhập kho', 'group' => 'phieu_nhap_kho'],
            ['name' => 'phieu_nhap_kho.xoa', 'display_name' => 'Xóa phiếu nhập kho', 'group' => 'phieu_nhap_kho'],

            // Nhóm Kiểm kê
            ['name' => 'kiem_ke.xem', 'display_name' => 'Xem kiểm kê', 'group' => 'kiem_ke'],
            ['name' => 'kiem_ke.tao', 'display_name' => 'Tạo kiểm kê', 'group' => 'kiem_ke'],
            ['name' => 'kiem_ke.sua', 'display_name' => 'Sửa kiểm kê', 'group' => 'kiem_ke'],
            ['name' => 'kiem_ke.xoa', 'display_name' => 'Xóa kiểm kê', 'group' => 'kiem_ke'],

            // Nhóm Thú cưng
            ['name' => 'thu_cung.xem', 'display_name' => 'Xem thú cưng', 'group' => 'thu_cung'],
            ['name' => 'thu_cung.tao', 'display_name' => 'Tạo thú cưng', 'group' => 'thu_cung'],
            ['name' => 'thu_cung.sua', 'display_name' => 'Sửa thú cưng', 'group' => 'thu_cung'],
            ['name' => 'thu_cung.xoa', 'display_name' => 'Xóa thú cưng', 'group' => 'thu_cung'],

            // Nhóm Dịch vụ
            ['name' => 'dich_vu.xem', 'display_name' => 'Xem dịch vụ', 'group' => 'dich_vu'],
            ['name' => 'dich_vu.tao', 'display_name' => 'Tạo dịch vụ', 'group' => 'dich_vu'],
            ['name' => 'dich_vu.sua', 'display_name' => 'Sửa dịch vụ', 'group' => 'dich_vu'],
            ['name' => 'dich_vu.xoa', 'display_name' => 'Xóa dịch vụ', 'group' => 'dich_vu'],

            // Nhóm Danh mục dịch vụ
            ['name' => 'danh_muc_dich_vu.xem', 'display_name' => 'Xem danh mục dịch vụ', 'group' => 'danh_muc_dich_vu'],
            ['name' => 'danh_muc_dich_vu.tao', 'display_name' => 'Tạo danh mục dịch vụ', 'group' => 'danh_muc_dich_vu'],
            ['name' => 'danh_muc_dich_vu.sua', 'display_name' => 'Sửa danh mục dịch vụ', 'group' => 'danh_muc_dich_vu'],
            ['name' => 'danh_muc_dich_vu.xoa', 'display_name' => 'Xóa danh mục dịch vụ', 'group' => 'danh_muc_dich_vu'],

            // Nhóm Khách hàng
            ['name' => 'khach_hang.xem', 'display_name' => 'Xem khách hàng', 'group' => 'khach_hang'],
            ['name' => 'khach_hang.sua', 'display_name' => 'Sửa khách hàng', 'group' => 'khach_hang'],

            // Nhóm Nhân viên
            ['name' => 'nhan_vien.xem', 'display_name' => 'Xem nhân viên', 'group' => 'nhan_vien'],
            ['name' => 'nhan_vien.tao', 'display_name' => 'Tạo nhân viên', 'group' => 'nhan_vien'],
            ['name' => 'nhan_vien.doi_mat_khau', 'display_name' => 'Đổi mật khẩu nhân viên', 'group' => 'nhan_vien'],
            ['name' => 'nhan_vien.khoa_tai_khoan', 'display_name' => 'Khóa tài khoản nhân viên', 'group' => 'nhan_vien'],
            ['name' => 'nhan_vien.mo_khoa_tai_khoan', 'display_name' => 'Mở khóa tài khoản nhân viên', 'group' => 'nhan_vien'],

            // Nhóm Khoa
            ['name' => 'khoa.tao', 'display_name' => 'Tạo khoa', 'group' => 'khoa'],

            // Nhóm Nhà cung cấp
            ['name' => 'nha_cung_cap.xem', 'display_name' => 'Xem nhà cung cấp', 'group' => 'nha_cung_cap'],
            ['name' => 'nha_cung_cap.tao', 'display_name' => 'Tạo nhà cung cấp', 'group' => 'nha_cung_cap'],
            ['name' => 'nha_cung_cap.sua', 'display_name' => 'Sửa nhà cung cấp', 'group' => 'nha_cung_cap'],
            ['name' => 'nha_cung_cap.xoa', 'display_name' => 'Xóa nhà cung cấp', 'group' => 'nha_cung_cap'],
            ['name' => 'nha_cung_cap.doi_trang_thai', 'display_name' => 'Đổi trạng thái nhà cung cấp', 'group' => 'nha_cung_cap'],

            // Nhóm Bài viết
            ['name' => 'bai_viet.xem', 'display_name' => 'Xem bài viết', 'group' => 'bai_viet'],
            ['name' => 'bai_viet.tao', 'display_name' => 'Tạo bài viết', 'group' => 'bai_viet'],
            ['name' => 'bai_viet.sua', 'display_name' => 'Sửa bài viết', 'group' => 'bai_viet'],
            ['name' => 'bai_viet.xoa', 'display_name' => 'Xóa bài viết', 'group' => 'bai_viet'],

            // Nhóm Phân loại bài viết
            ['name' => 'phan_loai_bai_viet.xem', 'display_name' => 'Xem phân loại bài viết', 'group' => 'phan_loai_bai_viet'],
            ['name' => 'phan_loai_bai_viet.tao', 'display_name' => 'Tạo phân loại bài viết', 'group' => 'phan_loai_bai_viet'],
            ['name' => 'phan_loai_bai_viet.sua', 'display_name' => 'Sửa phân loại bài viết', 'group' => 'phan_loai_bai_viet'],
            ['name' => 'phan_loai_bai_viet.xoa', 'display_name' => 'Xóa phân loại bài viết', 'group' => 'phan_loai_bai_viet'],

            // Nhóm Khuyến mãi
            ['name' => 'khuyen_mai.xem', 'display_name' => 'Xem khuyến mãi', 'group' => 'khuyen_mai'],
            ['name' => 'khuyen_mai.tao', 'display_name' => 'Tạo khuyến mãi', 'group' => 'khuyen_mai'],
            ['name' => 'khuyen_mai.sua', 'display_name' => 'Sửa khuyến mãi', 'group' => 'khuyen_mai'],
            ['name' => 'khuyen_mai.xoa', 'display_name' => 'Xóa khuyến mãi', 'group' => 'khuyen_mai'],
            ['name' => 'khuyen_mai.doi_trang_thai', 'display_name' => 'Đổi trạng thái khuyến mãi', 'group' => 'khuyen_mai'],

            // Nhóm Upload
            ['name' => 'upload.file', 'display_name' => 'Upload file', 'group' => 'upload'],
        ];

        foreach ($permissions as $permission) {
            \DB::table('permissions')->insert(array_merge($permission, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Gán quyền cho Admin (toàn quyền)
        $adminRole = \DB::table('roles')->where('name', 'admin')->first();
        $allPermissions = \DB::table('permissions')->get();
        
        foreach ($allPermissions as $permission) {
            \DB::table('role_permission')->insert([
                'role_id' => $adminRole->id,
                'permission_id' => $permission->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Gán quyền cho Bác sĩ
        $bacSiRole = \DB::table('roles')->where('name', 'bac_si')->first();
        $bacSiPermissions = [
            'lich_hen.xem', 'lich_hen.sua', 'lich_lam_viec.xem',
            'thu_cung.xem', 'thu_cung.sua', 'dich_vu.xem',
            'hang_hoa.xem', 'khach_hang.xem',
        ];
        
        foreach ($bacSiPermissions as $permName) {
            $perm = \DB::table('permissions')->where('name', $permName)->first();
            if ($perm) {
                \DB::table('role_permission')->insert([
                    'role_id' => $bacSiRole->id,
                    'permission_id' => $perm->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Gán quyền cho Y tá
        $ytaRole = \DB::table('roles')->where('name', 'y_ta')->first();
        $ytaPermissions = [
            'lich_hen.xem', 'lich_hen.xac_nhan', 'lich_lam_viec.xem',
            'hang_hoa.xem', 'hang_hoa.sua', 'phieu_nhap_kho.xem', 'phieu_nhap_kho.tao',
            'thu_cung.xem', 'khach_hang.xem', 'tai_chinh.thu_tien',
        ];
        
        foreach ($ytaPermissions as $permName) {
            $perm = \DB::table('permissions')->where('name', $permName)->first();
            if ($perm) {
                \DB::table('role_permission')->insert([
                    'role_id' => $ytaRole->id,
                    'permission_id' => $perm->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Gán quyền cho Lễ tân
        $leTanRole = \DB::table('roles')->where('name', 'le_tan')->first();
        $leTanPermissions = [
            'lich_hen.xem', 'lich_hen.tao', 'lich_hen.sua', 'lich_hen.xac_nhan',
            'khach_hang.xem', 'khach_hang.sua', 'thu_cung.xem', 'thu_cung.tao',
            'dich_vu.xem', 'tai_chinh.thu_tien',
        ];
        
        foreach ($leTanPermissions as $permName) {
            $perm = \DB::table('permissions')->where('name', $permName)->first();
            if ($perm) {
                \DB::table('role_permission')->insert([
                    'role_id' => $leTanRole->id,
                    'permission_id' => $perm->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        echo "✅ RBAC seeder completed successfully!\n";
    }
}
