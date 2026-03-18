<?php

namespace App\Http\Controllers;

use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PhanQuyenController extends Controller
{
    /**
     * Lấy danh sách tất cả vai trò và quyền
     */
    public function index()
    {
        try {
            $phanQuyens = PhanQuyen::all();

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách phân quyền thành công',
                'data' => $phanQuyens,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in PhanQuyenController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách phân quyền',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy chi tiết một vai trò
     */
    public function show($id)
    {
        try {
            $phanQuyen = PhanQuyen::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Lấy thông tin phân quyền thành công',
                'data' => $phanQuyen,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in PhanQuyenController@show: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phân quyền',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Cập nhật quyền cho vai trò
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $phanQuyen = PhanQuyen::findOrFail($id);

            // Cập nhật thông tin cơ bản và các quyền
            $phanQuyen->update($request->except(['ma_vai_tro', 'id', 'created_at', 'updated_at']));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật phân quyền thành công',
                'data' => $phanQuyen->fresh(),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in PhanQuyenController@update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật phân quyền',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy danh sách tất cả quyền có thể gán (cho giao diện)
     */
    public function getAllPermissions()
    {
        try {
            $permissions = [
                'lich_hen' => [
                    'label' => 'Nhóm Lịch hẹn',
                    'permissions' => [
                        'lich_hen_xem' => 'Xem',
                        'lich_hen_tao' => 'Tạo',
                        'lich_hen_sua' => 'Sửa',
                        'lich_hen_xoa' => 'Xóa',
                        'lich_hen_xac_nhan' => 'Xác nhận',
                    ],
                ],
                'lich_lam_viec' => [
                    'label' => 'Nhóm Lịch làm việc',
                    'permissions' => [
                        'lich_lam_viec_xem' => 'Xem',
                        'lich_lam_viec_tao' => 'Tạo',
                    ],
                ],
                'tai_chinh' => [
                    'label' => 'Nhóm Tài chính',
                    'permissions' => [
                        'tai_chinh_xem_doanh_thu' => 'Xem doanh thu',
                        'tai_chinh_thu_tien' => 'Thu tiền',
                        'tai_chinh_hoan_tien' => 'Hoàn tiền',
                    ],
                ],
                'phieu_chi' => [
                    'label' => 'Nhóm Phiếu chi',
                    'permissions' => [
                        'phieu_chi_xem' => 'Xem',
                        'phieu_chi_tao' => 'Tạo',
                        'phieu_chi_sua' => 'Sửa',
                        'phieu_chi_xoa' => 'Xóa',
                        'phieu_chi_xuat_pdf' => 'Xuất PDF',
                        'phieu_chi_thanh_toan' => 'Thanh toán',
                    ],
                ],
                'hang_hoa' => [
                    'label' => 'Nhóm Kho thuốc',
                    'permissions' => [
                        'hang_hoa_xem' => 'Xem tồn',
                        'hang_hoa_tao' => 'Tạo',
                        'hang_hoa_sua' => 'Sửa',
                        'hang_hoa_xoa' => 'Xóa',
                        'hang_hoa_doi_trang_thai' => 'Đổi trạng thái',
                    ],
                ],
                'phieu_nhap_kho' => [
                    'label' => 'Nhóm Nhập kho',
                    'permissions' => [
                        'phieu_nhap_kho_xem' => 'Xem',
                        'phieu_nhap_kho_tao' => 'Nhập kho',
                        'phieu_nhap_kho_doi_trang_thai' => 'Đổi trạng thái',
                        'phieu_nhap_kho_xuat_pdf' => 'Xuất PDF',
                        'phieu_nhap_kho_xoa' => 'Xóa',
                    ],
                ],
                'kiem_ke' => [
                    'label' => 'Nhóm Kiểm kê',
                    'permissions' => [
                        'kiem_ke_xem' => 'Xem',
                        'kiem_ke_tao' => 'Tạo',
                        'kiem_ke_sua' => 'Điều chỉnh',
                        'kiem_ke_xoa' => 'Xóa',
                    ],
                ],
                'thu_cung' => [
                    'label' => 'Nhóm Bệnh án',
                    'permissions' => [
                        'thu_cung_xem' => 'Xem chi tiết',
                        'thu_cung_tao' => 'Tạo',
                        'thu_cung_sua' => 'Kê đơn',
                        'thu_cung_xoa' => 'Xóa',
                    ],
                ],
                'other' => [
                    'label' => 'Khác',
                    'permissions' => [
                        'dich_vu_xem' => 'Xem dịch vụ',
                        'khach_hang_xem' => 'Xem khách hàng',
                        'nhan_vien_xem' => 'Xem nhân viên',
                        'upload_file' => 'Upload file',
                    ],
                ],
            ];

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách quyền thành công',
                'data' => $permissions,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in PhanQuyenController@getAllPermissions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
