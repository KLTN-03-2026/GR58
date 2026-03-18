<?php

namespace App\Http\Controllers;

use App\Models\KiemKe;
use App\Models\HangHoa;
use App\Models\ChiTietPhieuNhapKho;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class KiemKeController extends Controller
{
    /**
     * Display a listing of the resource.
     * Hiển thị danh sách kiểm kê với thông tin hàng hóa và chi tiết phiếu nhập kho
     */
    public function index(Request $request)
    {
        try {
            $query = KiemKe::with([
                'hangHoa:id,ma_hang_hoa,ten_mat_hang,don_vi_tinh,gia_ban,anh_san_pham',
                'chiTietPhieuNhapKho:id,phieu_nhap_kho_id,hang_hoa_id,so_luong,don_gia,thanh_tien,so_lo,han_su_dung',
                'chiTietPhieuNhapKho.phieuNhapKho:id,ma_phieu_nhap,ngay_nhap,tong_tien',
                'admin:id,ho_ten,email,anh_dai_dien',
                'nhanVien:id,full_name,email,anh_dai_dien,chuc_danh'
            ]);

            // Lọc theo hàng hóa
            if ($request->has('hang_hoa_id')) {
                $query->where('hang_hoa_id', $request->hang_hoa_id);
            }

            // Lọc theo trạng thái chênh lệch
            if ($request->has('trang_thai')) {
                $trangThai = $request->trang_thai;
                if ($trangThai == 'thieu') {
                    $query->where('chenh_lech', '<', 0);
                } elseif ($trangThai == 'thua') {
                    $query->where('chenh_lech', '>', 0);
                } elseif ($trangThai == 'dung') {
                    $query->where('chenh_lech', '=', 0);
                }
            }

            // Lọc theo ngày kiểm kê
            if ($request->has('tu_ngay')) {
                $query->where('ngay_kiem_ke', '>=', $request->tu_ngay);
            }
            if ($request->has('den_ngay')) {
                $query->where('ngay_kiem_ke', '<=', $request->den_ngay);
            }

            // Lọc theo người kiểm kê
            if ($request->has('nguoi_kiem_ke_type')) {
                $query->where('nguoi_kiem_ke_type', $request->nguoi_kiem_ke_type);
            }

            // Sắp xếp
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Phân trang
            $perPage = $request->get('per_page', 15);
            $kiemKes = $query->paginate($perPage);

            // Thêm thông tin tùy chỉnh cho mỗi bản ghi
            $kiemKes->getCollection()->transform(function ($kiemKe) {
                return [
                    'id' => $kiemKe->id,
                    'hang_hoa' => [
                        'id' => $kiemKe->hangHoa->id ?? null,
                        'ma_hang_hoa' => $kiemKe->hangHoa->ma_hang_hoa ?? null,
                        'ten_mat_hang' => $kiemKe->hangHoa->ten_mat_hang ?? null,
                        'don_vi_tinh' => $kiemKe->hangHoa->don_vi_tinh ?? null,
                        'gia_ban' => $kiemKe->hangHoa->gia_ban ?? null,
                        'anh_san_pham' => $kiemKe->hangHoa->anh_san_pham ?? null,
                    ],
                    'chi_tiet_phieu_nhap_kho' => $kiemKe->chiTietPhieuNhapKho ? [
                        'id' => $kiemKe->chiTietPhieuNhapKho->id,
                        'so_luong' => $kiemKe->chiTietPhieuNhapKho->so_luong,
                        'don_gia' => $kiemKe->chiTietPhieuNhapKho->don_gia,
                        'thanh_tien' => $kiemKe->chiTietPhieuNhapKho->thanh_tien,
                        'so_lo' => $kiemKe->chiTietPhieuNhapKho->so_lo,
                        'han_su_dung' => $kiemKe->chiTietPhieuNhapKho->han_su_dung,
                        'phieu_nhap_kho' => $kiemKe->chiTietPhieuNhapKho->phieuNhapKho ? [
                            'id' => $kiemKe->chiTietPhieuNhapKho->phieuNhapKho->id,
                            'ma_phieu_nhap' => $kiemKe->chiTietPhieuNhapKho->phieuNhapKho->ma_phieu_nhap,
                            'ngay_nhap' => $kiemKe->chiTietPhieuNhapKho->phieuNhapKho->ngay_nhap,
                            'tong_tien' => $kiemKe->chiTietPhieuNhapKho->phieuNhapKho->tong_tien,
                        ] : null,
                    ] : null,
                    'so_luong_he_thong' => $kiemKe->so_luong_he_thong,
                    'so_luong_thuc_te' => $kiemKe->so_luong_thuc_te,
                    'chenh_lech' => $kiemKe->chenh_lech,
                    'trang_thai_chenh_lech' => $kiemKe->trang_thai_chenh_lech,
                    'ly_do' => $kiemKe->ly_do,
                    'ngay_kiem_ke' => $kiemKe->ngay_kiem_ke,
                    'nguoi_kiem_ke_info' => $kiemKe->nguoi_kiem_ke_info,
                    'ghi_chu' => $kiemKe->ghi_chu,
                    'created_at' => $kiemKe->created_at,
                    'updated_at' => $kiemKe->updated_at,
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách kiểm kê thành công',
                'data' => $kiemKes
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi lấy danh sách kiểm kê',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * Thêm mới kiểm kê - tự động lấy người đăng nhập
     */
    public function store(Request $request)
    {
        try {
            // Validate dữ liệu
            $validator = Validator::make($request->all(), [
                'hang_hoa_id' => 'required|exists:hang_hoas,id',
                'chi_tiet_phieu_nhap_kho_id' => 'nullable|exists:chi_tiet_phieu_nhap_khos,id',
                'so_luong_he_thong' => 'required|integer|min:0',
                'so_luong_thuc_te' => 'required|integer|min:0',
                'ly_do' => 'nullable|string|max:255',
                'ngay_kiem_ke' => 'required|date',
                'ghi_chu' => 'nullable|string',
            ], [
                'hang_hoa_id.required' => 'Vui lòng chọn hàng hóa',
                'hang_hoa_id.exists' => 'Hàng hóa không tồn tại',
                'chi_tiet_phieu_nhap_kho_id.exists' => 'Chi tiết phiếu nhập kho không tồn tại',
                'so_luong_he_thong.required' => 'Vui lòng nhập số lượng hệ thống',
                'so_luong_he_thong.integer' => 'Số lượng hệ thống phải là số nguyên',
                'so_luong_he_thong.min' => 'Số lượng hệ thống phải lớn hơn hoặc bằng 0',
                'so_luong_thuc_te.required' => 'Vui lòng nhập số lượng thực tế',
                'so_luong_thuc_te.integer' => 'Số lượng thực tế phải là số nguyên',
                'so_luong_thuc_te.min' => 'Số lượng thực tế phải lớn hơn hoặc bằng 0',
                'ngay_kiem_ke.required' => 'Vui lòng chọn ngày kiểm kê',
                'ngay_kiem_ke.date' => 'Ngày kiểm kê không hợp lệ',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Tạo mới kiểm kê - Model sẽ tự động gán người kiểm kê từ user đăng nhập
            $kiemKe = KiemKe::create([
                'hang_hoa_id' => $request->hang_hoa_id,
                'chi_tiet_phieu_nhap_kho_id' => $request->chi_tiet_phieu_nhap_kho_id,
                'so_luong_he_thong' => $request->so_luong_he_thong,
                'so_luong_thuc_te' => $request->so_luong_thuc_te,
                'ly_do' => $request->ly_do,
                'ngay_kiem_ke' => $request->ngay_kiem_ke,
                'ghi_chu' => $request->ghi_chu,
            ]);

            // Load relationships
            $kiemKe->load([
                'hangHoa:id,ma_hang_hoa,ten_mat_hang,don_vi_tinh,gia_ban,anh_san_pham',
                'chiTietPhieuNhapKho:id,phieu_nhap_kho_id,hang_hoa_id,so_luong,don_gia,thanh_tien,so_lo,han_su_dung',
                'chiTietPhieuNhapKho.phieuNhapKho:id,ma_phieu_nhap,ngay_nhap,tong_tien',
                'admin:id,ho_ten,email,anh_dai_dien',
                'nhanVien:id,full_name,email,anh_dai_dien,chuc_danh'
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Thêm mới kiểm kê thành công',
                'data' => [
                    'id' => $kiemKe->id,
                    'hang_hoa' => [
                        'id' => $kiemKe->hangHoa->id ?? null,
                        'ma_hang_hoa' => $kiemKe->hangHoa->ma_hang_hoa ?? null,
                        'ten_mat_hang' => $kiemKe->hangHoa->ten_mat_hang ?? null,
                        'don_vi_tinh' => $kiemKe->hangHoa->don_vi_tinh ?? null,
                        'gia_ban' => $kiemKe->hangHoa->gia_ban ?? null,
                        'anh_san_pham' => $kiemKe->hangHoa->anh_san_pham ?? null,
                    ],
                    'chi_tiet_phieu_nhap_kho' => $kiemKe->chiTietPhieuNhapKho ? [
                        'id' => $kiemKe->chiTietPhieuNhapKho->id,
                        'so_luong' => $kiemKe->chiTietPhieuNhapKho->so_luong,
                        'don_gia' => $kiemKe->chiTietPhieuNhapKho->don_gia,
                        'thanh_tien' => $kiemKe->chiTietPhieuNhapKho->thanh_tien,
                        'so_lo' => $kiemKe->chiTietPhieuNhapKho->so_lo,
                        'han_su_dung' => $kiemKe->chiTietPhieuNhapKho->han_su_dung,
                    ] : null,
                    'so_luong_he_thong' => $kiemKe->so_luong_he_thong,
                    'so_luong_thuc_te' => $kiemKe->so_luong_thuc_te,
                    'chenh_lech' => $kiemKe->chenh_lech,
                    'trang_thai_chenh_lech' => $kiemKe->trang_thai_chenh_lech,
                    'ly_do' => $kiemKe->ly_do,
                    'ngay_kiem_ke' => $kiemKe->ngay_kiem_ke,
                    'nguoi_kiem_ke_info' => $kiemKe->nguoi_kiem_ke_info,
                    'ghi_chu' => $kiemKe->ghi_chu,
                    'created_at' => $kiemKe->created_at,
                    'updated_at' => $kiemKe->updated_at,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi thêm mới kiểm kê',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * Hiển thị chi tiết một kiểm kê
     */
    public function show($id)
    {
        try {
            $kiemKe = KiemKe::with([
                'hangHoa:id,ma_hang_hoa,ten_mat_hang,don_vi_tinh,gia_von,gia_ban,anh_san_pham,dinh_muc_toi_thieu',
                'chiTietPhieuNhapKho:id,phieu_nhap_kho_id,hang_hoa_id,so_luong,don_gia,thanh_tien,so_lo,han_su_dung,ghi_chu',
                'chiTietPhieuNhapKho.phieuNhapKho:id,ma_phieu_nhap,ngay_nhap,tong_tien,nha_cung_cap_id',
                'chiTietPhieuNhapKho.phieuNhapKho.nhaCungCap:id,ten_nha_cung_cap,so_dien_thoai',
                'admin:id,ho_ten,email,anh_dai_dien,so_dien_thoai',
                'nhanVien:id,full_name,email,anh_dai_dien,chuc_danh,phone'
            ])->find($id);

            if (!$kiemKe) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy kiểm kê'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Lấy thông tin kiểm kê thành công',
                'data' => [
                    'id' => $kiemKe->id,
                    'hang_hoa' => $kiemKe->hangHoa,
                    'chi_tiet_phieu_nhap_kho' => $kiemKe->chiTietPhieuNhapKho ? [
                        'id' => $kiemKe->chiTietPhieuNhapKho->id,
                        'so_luong' => $kiemKe->chiTietPhieuNhapKho->so_luong,
                        'don_gia' => $kiemKe->chiTietPhieuNhapKho->don_gia,
                        'thanh_tien' => $kiemKe->chiTietPhieuNhapKho->thanh_tien,
                        'so_lo' => $kiemKe->chiTietPhieuNhapKho->so_lo,
                        'han_su_dung' => $kiemKe->chiTietPhieuNhapKho->han_su_dung,
                        'ghi_chu' => $kiemKe->chiTietPhieuNhapKho->ghi_chu,
                        'phieu_nhap_kho' => $kiemKe->chiTietPhieuNhapKho->phieuNhapKho,
                    ] : null,
                    'so_luong_he_thong' => $kiemKe->so_luong_he_thong,
                    'so_luong_thuc_te' => $kiemKe->so_luong_thuc_te,
                    'chenh_lech' => $kiemKe->chenh_lech,
                    'trang_thai_chenh_lech' => $kiemKe->trang_thai_chenh_lech,
                    'ly_do' => $kiemKe->ly_do,
                    'ngay_kiem_ke' => $kiemKe->ngay_kiem_ke,
                    'nguoi_kiem_ke_info' => $kiemKe->nguoi_kiem_ke_info,
                    'ghi_chu' => $kiemKe->ghi_chu,
                    'created_at' => $kiemKe->created_at,
                    'updated_at' => $kiemKe->updated_at,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi lấy thông tin kiểm kê',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $kiemKe = KiemKe::find($id);

            if (!$kiemKe) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy kiểm kê'
                ], 404);
            }

            // Validate dữ liệu
            $validator = Validator::make($request->all(), [
                'hang_hoa_id' => 'sometimes|exists:hang_hoas,id',
                'chi_tiet_phieu_nhap_kho_id' => 'nullable|exists:chi_tiet_phieu_nhap_khos,id',
                'so_luong_he_thong' => 'sometimes|integer|min:0',
                'so_luong_thuc_te' => 'sometimes|integer|min:0',
                'ly_do' => 'nullable|string|max:255',
                'ngay_kiem_ke' => 'sometimes|date',
                'ghi_chu' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cập nhật kiểm kê
            $kiemKe->update($request->only([
                'hang_hoa_id',
                'chi_tiet_phieu_nhap_kho_id',
                'so_luong_he_thong',
                'so_luong_thuc_te',
                'ly_do',
                'ngay_kiem_ke',
                'ghi_chu'
            ]));

            // Load relationships
            $kiemKe->load([
                'hangHoa:id,ma_hang_hoa,ten_mat_hang,don_vi_tinh,gia_ban,anh_san_pham',
                'chiTietPhieuNhapKho:id,phieu_nhap_kho_id,hang_hoa_id,so_luong,don_gia,thanh_tien',
                'admin:id,ho_ten,email',
                'nhanVien:id,full_name,email,chuc_danh'
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật kiểm kê thành công',
                'data' => $kiemKe
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi cập nhật kiểm kê',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $kiemKe = KiemKe::find($id);

            if (!$kiemKe) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy kiểm kê'
                ], 404);
            }

            $kiemKe->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa kiểm kê thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi xóa kiểm kê',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
