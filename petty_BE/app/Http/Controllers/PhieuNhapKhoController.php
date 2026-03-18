<?php

namespace App\Http\Controllers;

use App\Models\PhieuNhapKho;
use App\Models\PhieuChi;
use App\Models\ChiTietPhieuNhapKho;
use App\Models\Admin;
use App\Models\NhanVien;
use App\Http\Requests\StorePhieuNhapKhoRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PhieuNhapKhoController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $phieuNhapKhos = PhieuNhapKho::with([
                'nhaCungCap:id,ten_nha_cung_cap,ma_nha_cung_cap',
                'nhanVien:id,full_name',
                'admin:id,ho_ten,email',
                'phieuChi:id,ma_phieu_chi,loai_phieu_chi,tong_so_tien,so_tien_thanh_toan_ngay,so_tien_con_no,trang_thai',
                'chiTietPhieuNhapKhos.hangHoa:id,ten_mat_hang,don_vi_tinh,gia_ban'
            ])->orderBy('created_at', 'desc')->get();

            $formattedData = $phieuNhapKhos->map(function ($phieu) {
                return [
                    'id' => $phieu->id,
                    'ma_phieu_nhap' => $phieu->ma_phieu_nhap,
                    'ngay_nhap' => $phieu->ngay_nhap,
                    'tong_tien' => $phieu->tong_tien,
                    'ghi_chu' => $phieu->ghi_chu,
                    'trang_thai' => $phieu->trang_thai,
                    'nha_cung_cap' => [
                        'id' => $phieu->nhaCungCap->id,
                        'ten_nha_cung_cap' => $phieu->nhaCungCap->ten_nha_cung_cap,
                        'ma_nha_cung_cap' => $phieu->nhaCungCap->ma_nha_cung_cap,
                    ],
                    'nhan_vien' => $phieu->nhanVien ? [
                        'id' => $phieu->nhanVien->id,
                        'full_name' => $phieu->nhanVien->full_name,
                    ] : null,
                    'admin' => $phieu->admin ? [
                        'id' => $phieu->admin->id,
                        'ho_ten' => $phieu->admin->ho_ten,
                        'email' => $phieu->admin->email,
                    ] : null,
                    'phieu_chi' => $phieu->phieuChi ? [
                        'id' => $phieu->phieuChi->id,
                        'ma_phieu_chi' => $phieu->phieuChi->ma_phieu_chi,
                        'loai_phieu_chi' => $phieu->phieuChi->loai_phieu_chi,
                        'tong_so_tien' => $phieu->phieuChi->tong_so_tien,
                        'so_tien_thanh_toan_ngay' => $phieu->phieuChi->so_tien_thanh_toan_ngay,
                        'so_tien_con_no' => $phieu->phieuChi->so_tien_con_no,
                        'trang_thai' => $phieu->phieuChi->trang_thai,
                    ] : null,
                    'chi_tiet' => $phieu->chiTietPhieuNhapKhos->map(function ($chiTiet) {
                        return [
                            'id' => $chiTiet->id,
                            'hang_hoa' => [
                                'id' => $chiTiet->hangHoa->id,
                                'ten_mat_hang' => $chiTiet->hangHoa->ten_mat_hang,
                                'don_vi_tinh' => $chiTiet->hangHoa->don_vi_tinh,
                                'gia_ban' => $chiTiet->hangHoa->gia_ban,
                            ],
                            'so_luong' => $chiTiet->so_luong,
                            'so_lo' => $chiTiet->so_lo,
                            'han_su_dung' => $chiTiet->han_su_dung,
                            'don_gia' => $chiTiet->don_gia,
                            'thanh_tien' => $chiTiet->thanh_tien,
                            'ghi_chu' => $chiTiet->ghi_chu,
                        ];
                    }),
                    'created_at' => $phieu->created_at,
                    'updated_at' => $phieu->updated_at,
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách phiếu nhập kho thành công',
                'data' => $formattedData
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách phiếu nhập kho: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách phiếu nhập kho',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StorePhieuNhapKhoRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Lấy thông tin user đang đăng nhập
            $user = $request->user();
            $nhanVienId = null;
            $adminId = null;

            // Kiểm tra loại user và set ID tương ứng
            if ($user instanceof Admin) {
                $adminId = $user->id;
            } elseif ($user instanceof NhanVien) {
                $nhanVienId = $user->id;
            }

            // Tạo phiếu chi với cấu trúc mới
            $phieuChi = PhieuChi::create([
                'ma_phieu_chi' => $request->input('phieu_chi.ma_phieu_chi'),
                'loai_phieu_chi' => 'chi_nhap_hang',
                'ly_do_chi' => $request->input('phieu_chi.ly_do', 'Chi nhập hàng'),
                'tong_so_tien' => 0, // Sẽ cập nhật sau
                'so_tien_thanh_toan_ngay' => 0,
                'so_tien_con_no' => 0,
                'tien_mat' => 0,
                'tien_chuyen_khoan' => 0,
                'anh_chung_tu' => $request->input('phieu_chi.anh_chung_tu', []),
                'nha_cung_cap_id' => $request->nha_cung_cap_id,
                'ngay_chi' => $request->input('phieu_chi.ngay_chi', now()),
                'ghi_chu' => $request->input('phieu_chi.ghi_chu'),
                'trang_thai' => 'con_no',
                'admin_id' => $adminId,
                'nhan_vien_id' => $nhanVienId,
            ]);

            $phieuNhapKho = PhieuNhapKho::create([
                'ma_phieu_nhap' => $request->ma_phieu_nhap,
                'ngay_nhap' => $request->ngay_nhap,
                'tong_tien' => 0,
                'ghi_chu' => $request->ghi_chu,
                'trang_thai' => $request->trang_thai ?? 'cho_duyet',
                'nha_cung_cap_id' => $request->nha_cung_cap_id,
                'phieu_chi_id' => $phieuChi->id,
                'nhan_vien_id' => $nhanVienId,
                'admin_id' => $adminId,
            ]);

            $tongTien = 0;
            foreach ($request->chi_tiet as $chiTiet) {
                $thanhTien = $chiTiet['so_luong'] * $chiTiet['don_gia'];
                $tongTien += $thanhTien;

                ChiTietPhieuNhapKho::create([
                    'phieu_nhap_kho_id' => $phieuNhapKho->id,
                    'hang_hoa_id' => $chiTiet['hang_hoa_id'],
                    'so_luong' => $chiTiet['so_luong'],
                    'so_lo' => $chiTiet['so_lo'],
                    'han_su_dung' => $chiTiet['han_su_dung'],
                    'don_gia' => $chiTiet['don_gia'],
                    'thanh_tien' => $thanhTien,
                    'ghi_chu' => $chiTiet['ghi_chu'],
                ]);
            }

            // Cập nhật tổng tiền cho phiếu nhập kho và phiếu chi
            $phieuNhapKho->update(['tong_tien' => $tongTien]);
            $phieuChi->update([
                'tong_so_tien' => $tongTien,
                'so_tien_con_no' => $tongTien, // Mặc định chưa thanh toán
            ]);

            $phieuNhapKho->load([
                'nhaCungCap:id,ten_nha_cung_cap,ma_nha_cung_cap',
                'nhanVien:id,full_name',
                'admin:id,ho_ten,email',
                'phieuChi:id,ma_phieu_chi,loai_phieu_chi,tong_so_tien,so_tien_thanh_toan_ngay,so_tien_con_no,trang_thai',
                'chiTietPhieuNhapKhos.hangHoa:id,ten_mat_hang,don_vi_tinh,gia_ban'
            ]);

            $response = [
                'id' => $phieuNhapKho->id,
                'ma_phieu_nhap' => $phieuNhapKho->ma_phieu_nhap,
                'ngay_nhap' => $phieuNhapKho->ngay_nhap,
                'tong_tien' => $phieuNhapKho->tong_tien,
                'ghi_chu' => $phieuNhapKho->ghi_chu,
                'trang_thai' => $phieuNhapKho->trang_thai,
                'nha_cung_cap' => [
                    'id' => $phieuNhapKho->nhaCungCap->id,
                    'ten_nha_cung_cap' => $phieuNhapKho->nhaCungCap->ten_nha_cung_cap,
                    'ma_nha_cung_cap' => $phieuNhapKho->nhaCungCap->ma_nha_cung_cap,
                ],
                'nhan_vien' => $phieuNhapKho->nhanVien ? [
                    'id' => $phieuNhapKho->nhanVien->id,
                    'full_name' => $phieuNhapKho->nhanVien->full_name,
                ] : null,
                'admin' => $phieuNhapKho->admin ? [
                    'id' => $phieuNhapKho->admin->id,
                    'ho_ten' => $phieuNhapKho->admin->ho_ten,
                    'email' => $phieuNhapKho->admin->email,
                ] : null,
                'phieu_chi' => [
                    'id' => $phieuNhapKho->phieuChi->id,
                    'ma_phieu_chi' => $phieuNhapKho->phieuChi->ma_phieu_chi,
                    'loai_phieu_chi' => $phieuNhapKho->phieuChi->loai_phieu_chi,
                    'tong_so_tien' => $phieuNhapKho->phieuChi->tong_so_tien,
                    'so_tien_thanh_toan_ngay' => $phieuNhapKho->phieuChi->so_tien_thanh_toan_ngay,
                    'so_tien_con_no' => $phieuNhapKho->phieuChi->so_tien_con_no,
                    'trang_thai' => $phieuNhapKho->phieuChi->trang_thai,
                ],
                'chi_tiet' => $phieuNhapKho->chiTietPhieuNhapKhos->map(function ($chiTiet) {
                    return [
                        'id' => $chiTiet->id,
                        'hang_hoa' => [
                            'id' => $chiTiet->hangHoa->id,
                            'ten_mat_hang' => $chiTiet->hangHoa->ten_mat_hang,
                            'don_vi_tinh' => $chiTiet->hangHoa->don_vi_tinh,
                            'gia_ban' => $chiTiet->hangHoa->gia_ban,
                        ],
                        'so_luong' => $chiTiet->so_luong,
                        'so_lo' => $chiTiet->so_lo,
                        'han_su_dung' => $chiTiet->han_su_dung,
                        'don_gia' => $chiTiet->don_gia,
                        'thanh_tien' => $chiTiet->thanh_tien,
                        'ghi_chu' => $chiTiet->ghi_chu,
                    ];
                }),
                'created_at' => $phieuNhapKho->created_at,
                'updated_at' => $phieuNhapKho->updated_at,
            ];

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Thêm phiếu nhập kho thành công',
                'data' => $response
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi thêm phiếu nhập kho: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi thêm phiếu nhập kho',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(PhieuNhapKho $phieuNhapKho): JsonResponse
    {
        try {
            $phieuNhapKho->load([
                'nhaCungCap:id,ten_nha_cung_cap,ma_nha_cung_cap',
                'nhanVien:id,full_name',
                'admin:id,ho_ten,email',
                'phieuChi:id,ma_phieu_chi,loai_phieu_chi,tong_so_tien,so_tien_thanh_toan_ngay,so_tien_con_no,trang_thai',
                'chiTietPhieuNhapKhos.hangHoa:id,ten_mat_hang,don_vi_tinh,gia_ban'
            ]);

            $response = [
                'id' => $phieuNhapKho->id,
                'ma_phieu_nhap' => $phieuNhapKho->ma_phieu_nhap,
                'ngay_nhap' => $phieuNhapKho->ngay_nhap,
                'tong_tien' => $phieuNhapKho->tong_tien,
                'ghi_chu' => $phieuNhapKho->ghi_chu,
                'trang_thai' => $phieuNhapKho->trang_thai,
                'nha_cung_cap' => [
                    'id' => $phieuNhapKho->nhaCungCap->id,
                    'ten_nha_cung_cap' => $phieuNhapKho->nhaCungCap->ten_nha_cung_cap,
                    'ma_nha_cung_cap' => $phieuNhapKho->nhaCungCap->ma_nha_cung_cap,
                ],
                'nhan_vien' => $phieuNhapKho->nhanVien ? [
                    'id' => $phieuNhapKho->nhanVien->id,
                    'full_name' => $phieuNhapKho->nhanVien->full_name,
                ] : null,
                'admin' => $phieuNhapKho->admin ? [
                    'id' => $phieuNhapKho->admin->id,
                    'ho_ten' => $phieuNhapKho->admin->ho_ten,
                    'email' => $phieuNhapKho->admin->email,
                ] : null,
                'phieu_chi' => $phieuNhapKho->phieuChi ? [
                    'id' => $phieuNhapKho->phieuChi->id,
                    'ma_phieu_chi' => $phieuNhapKho->phieuChi->ma_phieu_chi,
                    'loai_phieu_chi' => $phieuNhapKho->phieuChi->loai_phieu_chi,
                    'tong_so_tien' => $phieuNhapKho->phieuChi->tong_so_tien,
                    'so_tien_thanh_toan_ngay' => $phieuNhapKho->phieuChi->so_tien_thanh_toan_ngay,
                    'so_tien_con_no' => $phieuNhapKho->phieuChi->so_tien_con_no,
                    'trang_thai' => $phieuNhapKho->phieuChi->trang_thai,
                ] : null,
                'chi_tiet' => $phieuNhapKho->chiTietPhieuNhapKhos->map(function ($chiTiet) {
                    return [
                        'id' => $chiTiet->id,
                        'hang_hoa' => [
                            'id' => $chiTiet->hangHoa->id,
                            'ten_mat_hang' => $chiTiet->hangHoa->ten_mat_hang,
                            'don_vi_tinh' => $chiTiet->hangHoa->don_vi_tinh,
                            'gia_ban' => $chiTiet->hangHoa->gia_ban,
                        ],
                        'so_luong' => $chiTiet->so_luong,
                        'so_lo' => $chiTiet->so_lo,
                        'han_su_dung' => $chiTiet->han_su_dung,
                        'don_gia' => $chiTiet->don_gia,
                        'thanh_tien' => $chiTiet->thanh_tien,
                        'ghi_chu' => $chiTiet->ghi_chu,
                    ];
                }),
                'created_at' => $phieuNhapKho->created_at,
                'updated_at' => $phieuNhapKho->updated_at,
            ];

            return response()->json([
                'status' => true,
                'message' => 'Lấy thông tin phiếu nhập kho thành công',
                'data' => $response
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin phiếu nhập kho: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin phiếu nhập kho',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(PhieuNhapKho $phieuNhapKho): JsonResponse
    {
        try {
            DB::beginTransaction();
            $phieuNhapKho->chiTietPhieuNhapKhos()->delete();
            if ($phieuNhapKho->phieu_chi_id) {
                PhieuChi::find($phieuNhapKho->phieu_chi_id)->delete();
            }
            $phieuNhapKho->delete();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Xóa phiếu nhập kho thành công'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xóa phiếu nhập kho: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi xóa phiếu nhập kho',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changeStatus(PhieuNhapKho $phieuNhapKho, Request $request): JsonResponse
    {
        try {
            $request->validate([
                'trang_thai' => 'required|in:cho_duyet,da_duyet,da_huy'
            ]);

            DB::beginTransaction();

            $phieuNhapKho->update([
                'trang_thai' => $request->trang_thai
            ]);

            if ($phieuNhapKho->phieu_chi_id) {
                $phieuChi = PhieuChi::find($phieuNhapKho->phieu_chi_id);
                if ($phieuChi) {
                    $phieuChi->update([
                        'trang_thai' => $request->trang_thai
                    ]);
                }
            }

            DB::commit();

            $phieuNhapKho->load([
                'nhaCungCap:id,ten_nha_cung_cap,ma_nha_cung_cap',
                'nhanVien:id,full_name',
                'admin:id,ho_ten,email',
                'phieuChi:id,ma_phieu_chi,loai_phieu_chi,tong_so_tien,so_tien_thanh_toan_ngay,so_tien_con_no,trang_thai',
                'chiTietPhieuNhapKhos.hangHoa:id,ten_mat_hang,don_vi_tinh,gia_ban'
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Đổi trạng thái phiếu nhập kho thành công',
                'data' => [
                    'id' => $phieuNhapKho->id,
                    'ma_phieu_nhap' => $phieuNhapKho->ma_phieu_nhap,
                    'trang_thai' => $phieuNhapKho->trang_thai,
                    'trang_thai_phieu_chi' => $phieuNhapKho->phieuChi ? $phieuNhapKho->phieuChi->trang_thai : null,
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi đổi trạng thái phiếu nhập kho: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi đổi trạng thái phiếu nhập kho',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xuất phiếu nhập kho ra file PDF
     */
    public function exportPdf(PhieuNhapKho $phieuNhapKho)
    {
        try {
            // Load các relationships
            $phieuNhapKho->load([
                'nhaCungCap',
                'nhanVien',
                'admin',
                'phieuChi',
                'chiTietPhieuNhapKhos.hangHoa'
            ]);

            // Chuẩn bị dữ liệu cho PDF
            $data = [
                'phieu' => $phieuNhapKho,
                'ngay_xuat' => now()->format('d/m/Y H:i:s')
            ];

            // Tạo PDF với paper size A4
            $pdf = Pdf::loadView('pdf.phieu-nhap-kho', $data)
                ->setPaper('a4', 'portrait')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true);

            // Download PDF với tên file
            return $pdf->download('phieu-nhap-kho-' . $phieuNhapKho->ma_phieu_nhap . '.pdf');
        } catch (\Exception $e) {
            Log::error('Lỗi khi xuất PDF phiếu nhập kho: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi xuất PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
