<?php

namespace App\Http\Controllers;

use App\Models\HangHoa;
use Illuminate\Http\Request;

class HangHoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $hangHoas = HangHoa::with([
                'danhMuc',
                'chiTietPhieuNhapKhos' => function ($query) {
                    $query->select('id', 'hang_hoa_id', 'so_luong', 'don_gia', 'thanh_tien', 'so_lo', 'han_su_dung', 'phieu_nhap_kho_id')
                        ->with('phieuNhapKho:id,ma_phieu_nhap,ngay_nhap')
                        ->orderBy('created_at', 'desc')
                        ->limit(5); // Lấy 5 phiếu nhập gần nhất
                }
            ])->get()->map(function ($hangHoa) {
                // Tính tổng số lượng từ tất cả chi tiết phiếu nhập kho
                $tongSoLuongNhap = $hangHoa->chiTietPhieuNhapKhos->sum('so_luong');

                return array_merge($hangHoa->toArray(), [
                    'ten_danh_muc_hang_hoa' => $hangHoa->danhMuc?->ten_danh_muc_hang_hoa ?? null,
                    'tinh_trang_label' => $hangHoa->tinh_trang_label,
                    'tong_so_luong_nhap' => $tongSoLuongNhap,
                    'chi_tiet_nhap_kho_gan_nhat' => $hangHoa->chiTietPhieuNhapKhos->map(function ($ct) {
                        return [
                            'id' => $ct->id,
                            'so_luong' => $ct->so_luong,
                            'don_gia' => $ct->don_gia,
                            'thanh_tien' => $ct->thanh_tien,
                            'so_lo' => $ct->so_lo,
                            'han_su_dung' => $ct->han_su_dung,
                            'ma_phieu_nhap' => $ct->phieuNhapKho?->ma_phieu_nhap,
                            'ngay_nhap' => $ct->phieuNhapKho?->ngay_nhap,
                        ];
                    }),
                ]);
            });

            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách hàng hóa thành công',
                'data' => $hangHoas,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'ma_hang_hoa' => 'required|string|unique:hang_hoas,ma_hang_hoa',
                'ten_mat_hang' => 'required|string|max:255',
                'don_vi_tinh' => 'required|string|max:100',
                'gia_von' => 'required|numeric|min:0',
                'gia_ban' => 'required|numeric|min:0',
                'dinh_muc_toi_thieu' => 'required|integer|min:0',
                'anh_san_pham' => 'nullable|string',
                'tinh_trang' => 'required|string|in:hoat_dong,ngung_kinh_doanh',
                'danh_muc_hang_hoa_id' => 'nullable|exists:danh_muc_hang_hoas,id',
            ], [
                'tinh_trang.in' => 'Trạng thái phải là "hoat_dong" hoặc "ngung_kinh_doanh"',
            ]);

            $hangHoa = HangHoa::create($validated);
            $hangHoa->load('danhMuc');

            $response = array_merge($hangHoa->toArray(), [
                'ten_danh_muc_hang_hoa' => $hangHoa->danhMuc?->ten_danh_muc_hang_hoa ?? null,
                'tinh_trang_label' => $hangHoa->tinh_trang_label,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Thêm hàng hóa thành công',
                'data' => $response,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(HangHoa $hangHoa)
    {
        try {
            $hangHoa->load([
                'danhMuc',
                'chiTietPhieuNhapKhos' => function ($query) {
                    $query->select('id', 'hang_hoa_id', 'so_luong', 'don_gia', 'thanh_tien', 'so_lo', 'han_su_dung', 'phieu_nhap_kho_id', 'ghi_chu', 'created_at')
                        ->with('phieuNhapKho:id,ma_phieu_nhap,ngay_nhap,tong_tien,nha_cung_cap_id')
                        ->with('phieuNhapKho.nhaCungCap:id,ten_nha_cung_cap,so_dien_thoai')
                        ->orderBy('created_at', 'desc');
                }
            ]);

            // Tính tổng số lượng từ tất cả chi tiết phiếu nhập kho
            $tongSoLuongNhap = $hangHoa->chiTietPhieuNhapKhos->sum('so_luong');

            $response = array_merge($hangHoa->toArray(), [
                'ten_danh_muc_hang_hoa' => $hangHoa->danhMuc?->ten_danh_muc_hang_hoa ?? null,
                'tinh_trang_label' => $hangHoa->tinh_trang_label,
                'tong_so_luong_nhap' => $tongSoLuongNhap,
                'so_phieu_nhap' => $hangHoa->chiTietPhieuNhapKhos->count(),
                'chi_tiet_nhap_kho' => $hangHoa->chiTietPhieuNhapKhos->map(function ($ct) {
                    return [
                        'id' => $ct->id,
                        'so_luong' => $ct->so_luong,
                        'don_gia' => $ct->don_gia,
                        'thanh_tien' => $ct->thanh_tien,
                        'so_lo' => $ct->so_lo,
                        'han_su_dung' => $ct->han_su_dung,
                        'ghi_chu' => $ct->ghi_chu,
                        'ngay_tao' => $ct->created_at,
                        'phieu_nhap_kho' => $ct->phieuNhapKho ? [
                            'id' => $ct->phieuNhapKho->id,
                            'ma_phieu_nhap' => $ct->phieuNhapKho->ma_phieu_nhap,
                            'ngay_nhap' => $ct->phieuNhapKho->ngay_nhap,
                            'tong_tien' => $ct->phieuNhapKho->tong_tien,
                            'nha_cung_cap' => $ct->phieuNhapKho->nhaCungCap ? [
                                'id' => $ct->phieuNhapKho->nhaCungCap->id,
                                'ten_nha_cung_cap' => $ct->phieuNhapKho->nhaCungCap->ten_nha_cung_cap,
                                'so_dien_thoai' => $ct->phieuNhapKho->nhaCungCap->so_dien_thoai,
                            ] : null,
                        ] : null,
                    ];
                }),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Lấy chi tiết hàng hóa thành công',
                'data' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HangHoa $hangHoa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HangHoa $hangHoa)
    {
        try {
            $validated = $request->validate([
                'ma_hang_hoa' => 'sometimes|string|unique:hang_hoas,ma_hang_hoa,' . $hangHoa->id,
                'ten_mat_hang' => 'sometimes|string|max:255',
                'don_vi_tinh' => 'sometimes|string|max:100',
                'gia_von' => 'sometimes|numeric|min:0',
                'gia_ban' => 'sometimes|numeric|min:0',
                'dinh_muc_toi_thieu' => 'sometimes|integer|min:0',
                'anh_san_pham' => 'nullable|string',
                'tinh_trang' => 'sometimes|string|in:hoat_dong,ngung_kinh_doanh',
                'danh_muc_hang_hoa_id' => 'nullable|exists:danh_muc_hang_hoas,id',
            ], [
                'tinh_trang.in' => 'Trạng thái phải là "hoat_dong" hoặc "ngung_kinh_doanh"',
            ]);

            $hangHoa->update($validated);
            $hangHoa->load('danhMuc');

            $response = array_merge($hangHoa->toArray(), [
                'ten_danh_muc_hang_hoa' => $hangHoa->danhMuc?->ten_danh_muc_hang_hoa ?? null,
                'tinh_trang_label' => $hangHoa->tinh_trang_label,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật hàng hóa thành công',
                'data' => $response,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change the status of the product.
     */
    public function changeStatus(Request $request, HangHoa $hangHoa)
    {
        try {
            $request->validate([
                'tinh_trang' => 'required|string|in:hoat_dong,ngung_kinh_doanh',
            ], [
                'tinh_trang.in' => 'Trạng thái phải là "hoat_dong" hoặc "ngung_kinh_doanh"',
                'tinh_trang.required' => 'Trạng thái không được để trống',
            ]);

            $tinh_trang = $request->input('tinh_trang');

            $hangHoa->update(['tinh_trang' => $tinh_trang]);
            $hangHoa->load('danhMuc');

            $response = array_merge($hangHoa->toArray(), [
                'ten_danh_muc_hang_hoa' => $hangHoa->danhMuc?->ten_danh_muc_hang_hoa ?? null,
                'tinh_trang_label' => $hangHoa->tinh_trang_label,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Đổi trạng thái hàng hóa thành công',
                'data' => $response,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
