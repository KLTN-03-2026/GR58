<?php

namespace App\Http\Controllers;

use App\Models\ThuCung;
use App\Models\KhachHang;
use App\Models\PhieuKham;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HoSoBenhAnController extends Controller
{
    /**
     * Lấy danh sách hồ sơ bệnh án:
     * - Nhóm theo Khách hàng
     * - Mỗi KH có danh sách Thú cưng
     * - Mỗi Thú cưng có thông tin lần khám cuối & số lần khám
     */
    public function index(Request $request)
    {
        try {
            $search = $request->query('search', '');
            $type   = $request->query('type', 'all'); // all | member | vanglai

            // Lấy các thú cưng đã có ít nhất 1 phiếu khám (dùng hasManyThrough)
            $thuCungQuery = ThuCung::with([
                'khachHang',
            ])->whereHas('phieuKhams');

            // Tìm kiếm theo tên thú cưng, tên chủ, SĐT
            if ($search) {
                $thuCungQuery->where(function ($q) use ($search) {
                    $q->where('ten_thu_cung', 'like', "%{$search}%")
                      ->orWhereHas('khachHang', function ($q2) use ($search) {
                          $q2->where('full_name', 'like', "%{$search}%")
                             ->orWhere('phone', 'like', "%{$search}%");
                      });
                });
            }

            $allThuCungs = $thuCungQuery->get();

            // Group theo khách hàng
            $grouped = $allThuCungs->groupBy('khach_hang_id');

            $result = [];

            foreach ($grouped as $khachHangId => $pets) {
                $khachHang = $pets->first()->khachHang;
                if (!$khachHang) continue;

                $customerType = $khachHang->email ? 'member' : 'vanglai';

                // Filter by type
                if ($type === 'member' && $customerType !== 'member') continue;
                if ($type === 'vanglai' && $customerType !== 'vanglai') continue;

                $petsData = [];
                foreach ($pets as $pet) {
                    // Lấy tất cả phiếu khám của thú cưng này qua lich_hens
                    $phieuKhams = PhieuKham::whereHas('lichHen', function ($q) use ($pet) {
                            $q->where('thu_cung_id', $pet->id);
                        })
                        ->with(['nhanVien', 'lichHen'])
                        ->orderByDesc('created_at')
                        ->get();

                    if ($phieuKhams->isEmpty()) continue;

                    $latestKham = $phieuKhams->first();
                    $lastVisitDate = $latestKham->created_at;

                    // Tính "Cách đây X ngày/tháng"
                    $diffText = $this->formatDiffForHumans($lastVisitDate);

                    $petsData[] = [
                        'id'           => $pet->id,
                        'name'         => $pet->ten_thu_cung ?? 'Chưa đặt tên',
                        'species'      => $pet->loai_thu_cung ?? '',
                        'breed'        => $pet->giong_thu_cung ?? '',
                        'age'          => $pet->tuoi_thu_cung
                                            ? Carbon::parse($pet->tuoi_thu_cung)->age . ' tuổi'
                                            : 'Chưa rõ',
                        'gender'       => $pet->gioi_tinh === 'male' ? 'Đực'
                                            : ($pet->gioi_tinh === 'female' ? 'Cái' : 'Chưa rõ'),
                        'weight'       => $pet->can_nang
                                            ? (is_numeric($pet->can_nang) ? $pet->can_nang . ' kg' : $pet->can_nang)
                                            : 'Chưa rõ',
                        'image'        => $pet->anh_dai_dien
                                            ? asset('storage/' . $pet->anh_dai_dien)
                                            : null,
                        'lastVisit'    => $lastVisitDate->format('d/m/Y H:i'),
                        'lastVisitAgo' => $diffText,
                        'lastDiagnosis'=> $latestKham->chan_doan ?? 'Chưa có chẩn đoán',
                        'lastDoctor'   => $latestKham->nhanVien?->full_name ?? 'Chưa xác định',
                        'totalExams'   => $phieuKhams->count(),
                    ];
                }

                if (empty($petsData)) continue;

                $result[] = [
                    'id'    => $khachHang->id,
                    'name'  => $khachHang->full_name ?? 'Khách vãng lai',
                    'phone' => $khachHang->phone ?? '',
                    'email' => $khachHang->email ?? '',
                    'type'  => 'member', // Tất cả đã có tài khoản đều là member
                    'pets'  => $petsData,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách hồ sơ bệnh án thành công',
                'data'    => $result,
                'total'   => count($result),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách hồ sơ bệnh án',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy toàn bộ lịch sử khám bệnh của 1 thú cưng
     */
    public function lichSuKham($thuCungId)
    {
        try {
            $pet = ThuCung::with('khachHang')->find($thuCungId);
            if (!$pet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thú cưng',
                ], 404);
            }

            $phieuKhams = PhieuKham::whereHas('lichHen', function ($q) use ($thuCungId) {
                    $q->where('thu_cung_id', $thuCungId);
                })
                ->with(['nhanVien', 'lichHen.dichVu'])
                ->orderByDesc('created_at')
                ->get();

            $exams = $phieuKhams->map(function ($pk) {
                return [
                    'id'             => $pk->id,
                    'date'           => $pk->created_at->format('d/m/Y'),
                    'time'           => $pk->created_at->format('H:i'),
                    'ly_do_den_kham' => $pk->ly_do_den_kham ?? '',
                    'trieu_chung'    => $pk->trieu_chung ?? '',
                    'chan_doan'      => $pk->chan_doan ?? '',
                    'ghi_chu'        => $pk->ghi_chu ?? '',
                    'loai_chi_dinh'  => $pk->loai_chi_dinh ?? '',
                    'nhiet_do'       => $pk->nhiet_do,
                    'can_nang'       => $pk->can_nang,
                    'nhip_tim'       => $pk->nhip_tim,
                    'nhip_tho'       => $pk->nhip_tho,
                    'dich_vu'        => $pk->lichHen?->dichVu?->ten_dich_vu ?? null,
                    'bac_si'         => $pk->nhanVien?->full_name ?? 'Chưa xác định',
                    'bac_si_id'      => $pk->nhanVien?->id,
                ];
            });

            return response()->json([
                'success' => true,
                'data'    => [
                    'thu_cung' => [
                        'id'      => $pet->id,
                        'name'    => $pet->ten_thu_cung,
                        'species' => $pet->loai_thu_cung,
                        'breed'   => $pet->giong_thu_cung,
                        'image'   => $pet->anh_dai_dien ? asset('storage/' . $pet->anh_dai_dien) : null,
                    ],
                    'khach_hang' => [
                        'id'    => $pet->khachHang?->id,
                        'name'  => $pet->khachHang?->full_name,
                        'phone' => $pet->khachHang?->phone,
                    ],
                    'phieu_khams' => $exams,
                    'total'       => $exams->count(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy lịch sử khám',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format "cách đây X ngày/tuần/tháng"
     */
    private function formatDiffForHumans(Carbon $date): string
    {
        $now = Carbon::now();
        $diffDays = $date->diffInDays($now);

        if ($diffDays === 0) return 'Hôm nay';
        if ($diffDays === 1) return 'Hôm qua';
        if ($diffDays < 7)  return "{$diffDays} ngày trước";
        if ($diffDays < 30) {
            $weeks = (int)($diffDays / 7);
            return "{$weeks} tuần trước";
        }
        $months = (int)($diffDays / 30);
        return "{$months} tháng trước";
    }
}
