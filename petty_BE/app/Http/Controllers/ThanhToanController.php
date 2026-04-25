<?php

namespace App\Http\Controllers;

use App\Models\ThanhToan;
use App\Models\LichHen;
use App\Models\KhuyenMai;
use App\Models\ChiTietKhuyenMai;
use App\Models\NhanVien;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ThanhToanController extends Controller
{
    // ----------------------------------------------------------------
    // Tạo đơn thanh toán (Nurse thu tiền sau khi khám xong)
    // ----------------------------------------------------------------
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'lich_hen_id'           => 'required|exists:lich_hens,id',
            'hinh_thuc_thanh_toan'  => 'required|in:tien_mat,vnpay,momo,ket_hop',
            'tien_mat'              => 'nullable|numeric|min:0',
            'tien_online'           => 'nullable|numeric|min:0',
            'ma_giam_gia'           => 'nullable|string',
            'ghi_chu'               => 'nullable|string',
        ]);

        $user = $request->user();

        DB::beginTransaction();
        try {
            $lichHen = LichHen::with(['dichVus', 'khachHang', 'thuCung'])->findOrFail($request->lich_hen_id);

            // Kiểm tra lịch hẹn đã hoàn thành khám chưa
            if ($lichHen->trang_thai !== 'completed') {
                return response()->json([
                    'status'  => false,
                    'message' => 'Lịch hẹn chưa hoàn thành khám, không thể thanh toán.',
                ], 422);
            }

            // Kiểm tra đã thanh toán chưa
            if ($lichHen->thanh_toan_id) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Lịch hẹn này đã được thanh toán.',
                ], 422);
            }

            // ── Tính tổng tiền gốc từ các dịch vụ ──
            $tongTienGoc = $lichHen->dichVus->sum(function ($dv) {
                return $dv->pivot->thanh_tien ?? $dv->gia_tien;
            });

            // Nếu chưa có dịch vụ trong pivot, lấy từ dich_vu_id gốc
            if ($tongTienGoc == 0 && $lichHen->dich_vu_id) {
                $tongTienGoc = $lichHen->dichVu?->gia_tien ?? 0;
            }

            // ── Xử lý khuyến mãi ──
            $soTienGiam  = 0;
            $khuyenMaiId = null;
            $loaiGiam    = 'khong_giam';
            $maGiamGia   = null;

            if ($request->filled('ma_giam_gia')) {
                // Khuyến mãi theo mã code
                $km = KhuyenMai::where('ma_code', $request->ma_giam_gia)
                    ->where('trang_thai', 'dang_chay')
                    ->where('loai_khuyen_mai', 'ma_giam_gia')
                    ->where('tu_ngay', '<=', now())
                    ->where('den_ngay', '>=', now())
                    ->first();

                if ($km) {
                    // Kiểm tra giá trị đơn tối thiểu
                    if ($km->gia_tri_don_toi_thieu && $tongTienGoc < $km->gia_tri_don_toi_thieu) {
                        return response()->json([
                            'status'  => false,
                            'message' => 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($km->gia_tri_don_toi_thieu) . 'đ để dùng mã này.',
                        ], 422);
                    }

                    // Tính tiền giảm
                    if ($km->hinh_thuc_giam === 'phan_tram') {
                        $soTienGiam = ($tongTienGoc * $km->gia_tri_giam) / 100;
                        if ($km->giam_toi_da) {
                            $soTienGiam = min($soTienGiam, $km->giam_toi_da);
                        }
                    } else {
                        $soTienGiam = min($km->gia_tri_giam, $tongTienGoc);
                    }

                    $khuyenMaiId = $km->id;
                    $loaiGiam    = 'ma_code';
                    $maGiamGia   = $request->ma_giam_gia;

                    // Cập nhật số lượng đã dùng
                    $km->increment('so_luong_da_dung');

                } else {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.',
                    ], 422);
                }

            } else {
                // Khuyến mãi tự động theo rank khách hàng
                $km = $this->getAutoPromoByRank($lichHen->khachHang?->rank, $tongTienGoc);
                if ($km) {
                    $soTienGiam  = $this->calcDiscount($km, $tongTienGoc);
                    $khuyenMaiId = $km->id;
                    $loaiGiam    = 'rank';
                }
            }

            $tongTienSauGiam = max(0, $tongTienGoc - $soTienGiam);

            // ── Tạo bản ghi thanh toán ──
            $thanhToan = ThanhToan::create([
                'ma_thanh_toan'         => 'TT' . now()->format('ymdHis') . rand(10, 99),
                'lich_hen_id'           => $lichHen->id,
                'khach_hang_id'         => $lichHen->khach_hang_id,
                'tong_tien_goc'         => $tongTienGoc,
                'so_tien_giam'          => $soTienGiam,
                'tong_tien_sau_giam'    => $tongTienSauGiam,
                'hinh_thuc_thanh_toan'  => $request->hinh_thuc_thanh_toan,
                'tien_mat'              => $request->tien_mat ?? 0,
                'tien_online'           => $request->tien_online ?? 0,
                'khuyen_mai_id'         => $khuyenMaiId,
                'ma_giam_gia'           => $maGiamGia,
                'loai_giam'             => $loaiGiam,
                'trang_thai'            => 'da_thanh_toan',
                'nhan_vien_id'          => $user instanceof NhanVien ? $user->id : null,
                'admin_id'              => $user instanceof Admin ? $user->id : null,
                'ngay_thanh_toan'       => now(),
                'ghi_chu'               => $request->ghi_chu,
            ]);

            // ── Ghi lịch sử dùng khuyến mãi ──
            if ($khuyenMaiId) {
                ChiTietKhuyenMai::create([
                    'thanh_toan_id'  => $thanhToan->id,
                    'khuyen_mai_id'  => $khuyenMaiId,
                    'khach_hang_id'  => $lichHen->khach_hang_id,
                    'so_tien_giam'   => $soTienGiam,
                    'ngay_su_dung'   => now(),
                ]);
            }

            // ── Cập nhật thanh_toan_id vào lịch hẹn ──
            $lichHen->update(['thanh_toan_id' => $thanhToan->id]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Thanh toán thành công!',
                'data'    => $thanhToan->load(['lichHen', 'khachHang', 'khuyenMai']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Lỗi thanh toán: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ----------------------------------------------------------------
    // Xem trước hóa đơn (preview trước khi xác nhận)
    // ----------------------------------------------------------------
    public function preview(Request $request): JsonResponse
    {
        $request->validate([
            'lich_hen_id' => 'required|exists:lich_hens,id',
            'ma_giam_gia' => 'nullable|string',
        ]);

        $lichHen     = LichHen::with(['dichVus', 'khachHang', 'thuCung', 'dichVu'])->findOrFail($request->lich_hen_id);
        $tongTienGoc = $lichHen->dichVus->sum(fn($dv) => $dv->pivot->thanh_tien ?? $dv->gia_tien);

        if ($tongTienGoc == 0 && $lichHen->dich_vu_id) {
            $tongTienGoc = $lichHen->dichVu?->gia_tien ?? 0;
        }

        $soTienGiam  = 0;
        $khuyenMai   = null;
        $loaiGiam    = 'khong_giam';
        $error       = null;

        if ($request->filled('ma_giam_gia')) {
            $km = KhuyenMai::where('ma_code', $request->ma_giam_gia)
                ->where('trang_thai', 'dang_chay')
                ->where('tu_ngay', '<=', now())
                ->where('den_ngay', '>=', now())
                ->first();

            if ($km) {
                if (!$km->gia_tri_don_toi_thieu || $tongTienGoc >= $km->gia_tri_don_toi_thieu) {
                    $soTienGiam = $this->calcDiscount($km, $tongTienGoc);
                    $khuyenMai  = $km;
                    $loaiGiam   = 'ma_code';
                } else {
                    $error = 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($km->gia_tri_don_toi_thieu) . 'đ';
                }
            } else {
                $error = 'Mã giảm giá không hợp lệ hoặc đã hết hạn';
            }
        } else {
            $km = $this->getAutoPromoByRank($lichHen->khachHang?->rank, $tongTienGoc);
            if ($km) {
                $soTienGiam = $this->calcDiscount($km, $tongTienGoc);
                $khuyenMai  = $km;
                $loaiGiam   = 'rank';
            }
        }

        return response()->json([
            'status' => true,
            'data'   => [
                'lich_hen'           => [
                    'id'          => $lichHen->id,
                    'ngay_gio'    => $lichHen->ngay_gio,
                    'khach_hang'  => $lichHen->khachHang?->only(['id', 'full_name', 'phone', 'rank']),
                    'thu_cung'    => $lichHen->thuCung?->only(['id', 'ten_thu_cung', 'loai_thu_cung']),
                    'dich_vus'    => $lichHen->dichVus->map(fn($dv) => [
                        'ten'        => $dv->ten,
                        'don_gia'    => $dv->pivot->don_gia ?? $dv->gia_tien,
                        'so_luong'   => $dv->pivot->so_luong ?? 1,
                        'thanh_tien' => $dv->pivot->thanh_tien ?? $dv->gia_tien,
                    ]),
                ],
                'tong_tien_goc'      => $tongTienGoc,
                'so_tien_giam'       => $soTienGiam,
                'tong_tien_sau_giam' => max(0, $tongTienGoc - $soTienGiam),
                'khuyen_mai'         => $khuyenMai ? [
                    'ten'        => $khuyenMai->ten_khuyen_mai,
                    'loai_giam'  => $loaiGiam,
                    'gia_tri'    => $khuyenMai->gia_tri_giam,
                    'hinh_thuc'  => $khuyenMai->hinh_thuc_giam,
                ] : null,
                'error' => $error,
            ],
        ]);
    }

    // ----------------------------------------------------------------
    // Chi tiết thanh toán
    // ----------------------------------------------------------------
    public function show($id): JsonResponse
    {
        $thanhToan = ThanhToan::with(['lichHen.thuCung', 'khachHang', 'khuyenMai', 'nhanVien'])
            ->findOrFail($id);

        return response()->json(['status' => true, 'data' => $thanhToan]);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------
    private function getAutoPromoByRank(?string $rank, float $tongTien): ?KhuyenMai
    {
        if (!$rank) return null;

        $loaiKhachHang = ($rank === 'Silver') ? 'tat_ca' : 'vip';

        return KhuyenMai::where('loai_khuyen_mai', 'chuong_trinh_tu_dong')
            ->where('trang_thai', 'dang_chay')
            ->where('tu_ngay', '<=', now())
            ->where('den_ngay', '>=', now())
            ->where(function ($q) use ($loaiKhachHang) {
                $q->where('loai_khach_hang', 'tat_ca')
                  ->orWhere('loai_khach_hang', $loaiKhachHang);
            })
            ->where(function ($q) use ($tongTien) {
                $q->whereNull('gia_tri_don_toi_thieu')
                  ->orWhere('gia_tri_don_toi_thieu', '<=', $tongTien);
            })
            ->orderByDesc('gia_tri_giam')
            ->first();
    }

    private function calcDiscount(KhuyenMai $km, float $tongTien): float
    {
        if ($km->hinh_thuc_giam === 'phan_tram') {
            $giam = ($tongTien * $km->gia_tri_giam) / 100;
            return $km->giam_toi_da ? min($giam, $km->giam_toi_da) : $giam;
        }
        return min($km->gia_tri_giam, $tongTien);
    }
}