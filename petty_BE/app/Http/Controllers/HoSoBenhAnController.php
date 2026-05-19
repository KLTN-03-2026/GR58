<?php

namespace App\Http\Controllers;

use App\Models\ThuCung;
use App\Models\PhieuKham;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Helpers\PetImageHelper;

class HoSoBenhAnController extends Controller
{
    private function mapOwnerType($khachHang): string
    {
        return $khachHang && !empty($khachHang->email) ? 'member' : 'vanglai';
    }

    private function mapExamItem(PhieuKham $pk): array
    {
        $serviceNames = [];
        if ($pk->relationLoaded('lichHen') && $pk->lichHen) {
            if ($pk->lichHen->relationLoaded('dichVus')) {
                $serviceNames = $pk->lichHen->dichVus->pluck('ten')->filter()->values()->all();
            } elseif ($pk->lichHen->relationLoaded('dichVu') && $pk->lichHen->dichVu) {
                $serviceNames = [$pk->lichHen->dichVu->ten];
            }
        }

        return [
            'id' => $pk->id,
            'created_at' => $pk->created_at?->format('Y-m-d H:i:s'),
            'date' => $pk->created_at?->format('d/m/Y'),
            'time' => $pk->created_at?->format('H:i'),
            'reason' => $pk->ly_do_den_kham ?? '',
            'symptoms' => $pk->trieu_chung ?? '',
            'diagnosis' => $pk->chan_doan ?? '',
            'notes' => $pk->ghi_chu ?? '',
            'referral_type' => $pk->loai_chi_dinh ?? '',
            'vital_signs' => [
                'temperature' => $pk->nhiet_do,
                'weight' => $pk->can_nang,
                'heart_rate' => $pk->nhip_tim,
                'respiratory_rate' => $pk->nhip_tho,
            ],
            'prescription' => is_array($pk->don_thuoc) ? $pk->don_thuoc : [],
            'doctor' => [
                'id' => $pk->nhanVien?->id,
                'name' => $pk->nhanVien?->full_name ?? 'Chưa xác định',
            ],
            'services' => $serviceNames,
        ];
    }

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

                $customerType = $this->mapOwnerType($khachHang);

                // Filter by type
                if ($type === 'member' && $customerType !== 'member') continue;
                if ($type === 'vanglai' && $customerType !== 'vanglai') continue;

                $petsData = [];
                foreach ($pets as $pet) {
                    // Lấy tất cả phiếu khám của thú cưng này qua lich_hens
                    $phieuKhams = PhieuKham::whereHas('lichHen', function ($q) use ($pet) {
                            $q->where('thu_cung_id', $pet->id);
                        })
                        ->with(['nhanVien', 'lichHen.dichVu', 'lichHen.dichVus'])
                        ->orderByDesc('updated_at')
                        ->orderByDesc('id')
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
                        'image'        => $pet->anh_dai_dien_url
                                            ?? PetImageHelper::getDefaultImage($pet->loai_thu_cung ?? 'khac', $pet->gioi_tinh),
                        'lastVisit'    => $lastVisitDate->format('d/m/Y H:i'),
                        'lastVisitAgo' => $diffText,
                        'lastDiagnosis'=> $latestKham->chan_doan ?? 'Chưa có chẩn đoán',
                        'lastDoctor'   => $latestKham->nhanVien?->full_name ?? 'Chưa xác định',
                        'totalExams'   => $phieuKhams->count(),
                        'latest_exam_id' => $latestKham->id,
                    ];
                }

                if (empty($petsData)) continue;

                $result[] = [
                    'id'    => $khachHang->id,
                    'name'  => $khachHang->full_name ?? 'Khách vãng lai',
                    'phone' => $khachHang->phone ?? '',
                    'email' => $khachHang->email ?? '',
                    'type'  => $customerType,
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
                ->with(['nhanVien', 'lichHen.dichVu', 'lichHen.dichVus'])
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->get();

            $exams = $phieuKhams->map(fn ($pk) => $this->mapExamItem($pk))->values();
            $latestExam = $exams->first();

            $weightHistory = $exams
                ->filter(fn ($exam) => !is_null($exam['vital_signs']['weight']))
                ->map(function ($exam) {
                    return [
                        'date' => $exam['date'],
                        'value' => (float) $exam['vital_signs']['weight'],
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'data'    => [
                    'thu_cung' => [
                        'id'      => $pet->id,
                        'name'    => $pet->ten_thu_cung,
                        'species' => $pet->loai_thu_cung,
                        'breed'   => $pet->giong_thu_cung,
                        'image'   => $pet->anh_dai_dien_url
                            ?? PetImageHelper::getDefaultImage($pet->loai_thu_cung ?? 'khac', $pet->gioi_tinh),
                        'gender'  => $pet->gioi_tinh,
                        'weight'  => $pet->can_nang,
                        'note'    => $pet->ghi_chu ?? '',
                    ],
                    'khach_hang' => [
                        'id'    => $pet->khachHang?->id,
                        'name'  => $pet->khachHang?->full_name,
                        'phone' => $pet->khachHang?->phone,
                        'address' => $pet->khachHang?->address,
                        'type' => $this->mapOwnerType($pet->khachHang),
                    ],
                    'summary' => [
                        'total_exams' => $exams->count(),
                        'latest_diagnosis' => $latestExam['diagnosis'] ?? 'Chưa có chẩn đoán',
                        'latest_visit' => $latestExam['date'] ?? null,
                    ],
                    'history' => $exams,
                    'weight_history' => $weightHistory,
                    'latest_exam' => $latestExam ?? [
                        'id' => null,
                        'created_at' => null,
                        'date' => null,
                        'time' => null,
                        'reason' => '',
                        'symptoms' => '',
                        'diagnosis' => 'Chưa có dữ liệu khám',
                        'notes' => '',
                        'referral_type' => '',
                        'vital_signs' => [
                            'temperature' => null,
                            'weight' => null,
                            'heart_rate' => null,
                            'respiratory_rate' => null,
                        ],
                        'prescription' => [],
                        'doctor' => ['id' => null, 'name' => 'Chưa xác định'],
                        'services' => [],
                    ],
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
