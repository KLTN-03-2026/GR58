<?php

namespace App\Services;

use App\Models\DichVu;
use App\Models\KhachHang;
use App\Models\LichHen;
use App\Models\LichLamViec;
use App\Models\ThuCung;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ChatbotFunctionService
{
    public function getToolDefinitions(): array
    {
        return [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_my_pets',
                    'description' => 'Lấy danh sách tất cả thú cưng của khách hàng đang chat',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object) [],
                        'required' => [],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_pet_medical_history',
                    'description' => 'Lấy lịch sử khám bệnh gần nhất (3 lần) của một thú cưng cụ thể',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'pet_id' => [
                                'type' => 'string',
                                'description' => 'ID số của thú cưng cần xem lịch sử khám',
                            ],
                        ],
                        'required' => ['pet_id'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_upcoming_appointments',
                    'description' => 'Lấy danh sách lịch hẹn sắp tới của khách hàng',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object) [],
                        'required' => [],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_available_slots',
                    'description' => 'Kiểm tra khung giờ còn trống để đặt lịch hẹn cho một ngày cụ thể',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'date' => [
                                'type' => 'string',
                                'description' => 'Ngày cần kiểm tra, format YYYY-MM-DD',
                            ],
                        ],
                        'required' => ['date'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_services',
                    'description' => 'Lấy danh sách tất cả dịch vụ khám/chăm sóc thú cưng hiện có tại phòng khám, kèm ID để đặt lịch',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object) [],
                        'required' => [],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'book_appointment',
                    'description' => 'Đặt lịch hẹn khám bệnh cho thú cưng',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'thu_cung_id' => [
                                'type' => 'string',
                                'description' => 'ID số của thú cưng cần đặt lịch',
                            ],
                            'ngay_gio' => [
                                'type' => 'string',
                                'description' => 'Ngày giờ hẹn, format YYYY-MM-DD HH:00',
                            ],
                            'dich_vu_id' => [
                                'type' => 'string',
                                'description' => 'ID số của dịch vụ cần đặt',
                            ],
                            'ghi_chu' => [
                                'type' => 'string',
                                'description' => 'Ghi chú thêm cho lịch hẹn (không bắt buộc)',
                            ],
                        ],
                        'required' => ['thu_cung_id', 'ngay_gio', 'dich_vu_id'],
                    ],
                ],
            ],
        ];
    }

    public function executeFunction(string $name, array $args, KhachHang $user): array
    {
        return match ($name) {
            'get_my_pets' => $this->getMyPets($user),
            'get_pet_medical_history' => $this->getPetMedicalHistory($args, $user),
            'get_upcoming_appointments' => $this->getUpcomingAppointments($user),
            'get_available_slots' => $this->getAvailableSlots($args),
            'get_services' => $this->getServices(),
            'book_appointment' => $this->bookAppointment($args, $user),
            default => ['error' => 'Chức năng không được hỗ trợ: ' . $name],
        };
    }

    private function getMyPets(KhachHang $user): array
    {
        $pets = $user->thuCungs()->get();

        return [
            'pets' => $pets->map(function ($pet) {
                return [
                    'id' => $pet->id,
                    'ten_thu_cung' => $pet->ten_thu_cung,
                    'loai' => $pet->loai_thu_cung,
                    'giong' => $pet->giong_thu_cung,
                    'tuoi' => $pet->tuoi_thu_cung
                        ? Carbon::parse($pet->tuoi_thu_cung)->age . ' tuổi'
                        : 'không rõ',
                    'can_nang' => $pet->can_nang ? $pet->can_nang . 'kg' : null,
                    'gioi_tinh' => $pet->gioi_tinh,
                ];
            })->values()->all(),
        ];
    }

    private function getPetMedicalHistory(array $args, KhachHang $user): array
    {
        $petId = isset($args['pet_id']) ? (int) $args['pet_id'] : null;

        if (! $petId) {
            return ['error' => 'Thiếu pet_id'];
        }

        $pet = ThuCung::where('id', $petId)
            ->where('khach_hang_id', $user->id)
            ->first();

        if (! $pet) {
            return ['error' => 'Không tìm thấy thú cưng'];
        }

        $phieuKhams = $pet->phieuKhams()
            ->with('nhanVien')
            ->orderByDesc('phieu_khams.created_at')
            ->limit(3)
            ->get();

        return [
            'pet' => $pet->ten_thu_cung,
            'records' => $phieuKhams->map(function ($pk) {
                return [
                    'ngay' => $pk->created_at ? $pk->created_at->format('d/m/Y') : null,
                    'ly_do_den_kham' => $pk->ly_do_den_kham,
                    'trieu_chung' => $pk->trieu_chung,
                    'chan_doan' => $pk->chan_doan,
                    'bac_si' => $pk->nhanVien->ho_ten ?? null,
                ];
            })->values()->all(),
        ];
    }

    private function getUpcomingAppointments(KhachHang $user): array
    {
        $appointments = LichHen::where('khach_hang_id', $user->id)
            ->where('trang_thai', '!=', 'da_huy')
            ->where('ngay_gio', '>=', Carbon::now())
            ->with(['thuCung', 'dichVu'])
            ->orderBy('ngay_gio')
            ->limit(10)
            ->get();

        return [
            'appointments' => $appointments->map(function ($appt) {
                return [
                    'id' => $appt->id,
                    'ngay_gio' => $appt->ngay_gio ? $appt->ngay_gio->format('d/m/Y H:i') : null,
                    'thu_cung' => $appt->thuCung->ten_thu_cung ?? 'N/A',
                    'dich_vu' => $appt->dichVu->ten ?? 'Khám tổng quát',
                    'trang_thai' => $appt->trang_thai,
                ];
            })->values()->all(),
        ];
    }

    private function getAvailableSlots(array $args): array
    {
        $dateStr = $args['date'] ?? null;

        if (! $dateStr) {
            return ['error' => 'Thiếu ngày (date)'];
        }

        $date = Carbon::parse($dateStr)->startOfDay();

        if ($date->lt(Carbon::today())) {
            return ['error' => 'Ngày đã qua, vui lòng chọn ngày trong tương lai'];
        }

        $dateFormatted = $date->toDateString();
        $allShifts = LichLamViec::where('ngay_lam', $dateFormatted)
            ->whereHas('nhanVien', fn ($q) => $q->where('vai_tro', 'bac_si'))
            ->get();

        if ($allShifts->isEmpty()) {
            return ['slots' => [], 'message' => 'Phòng khám không có lịch làm việc cho ngày này'];
        }

        $slots = [];
        for ($hour = 8; $hour <= 16; $hour++) {
            if ($hour === 12) {
                continue;
            }

            $slotTime = Carbon::parse($dateFormatted . ' ' . sprintf('%02d:00:00', $hour));

            $capacity = $allShifts->filter(function ($shift) use ($hour) {
                if ($shift->thoi_gian_truc === LichLamViec::CA_SANG) {
                    return $hour >= 8 && $hour <= 16;
                }
                if ($shift->thoi_gian_truc === LichLamViec::CA_CHIEU) {
                    return $hour >= 13 && $hour <= 16;
                }
                return false;
            })->count();

            if ($capacity === 0) {
                continue;
            }

            $booked = LichHen::whereIn('trang_thai', ['confirmed', 'in-progress'])
                ->where('ngay_gio', '>=', $slotTime->copy()->subMinutes(59)->format('Y-m-d H:i:s'))
                ->where('ngay_gio', '<', $slotTime->copy()->addMinutes(60)->format('Y-m-d H:i:s'))
                ->whereDate('ngay_gio', $dateFormatted)
                ->count();

            if ($booked < $capacity) {
                $slots[] = sprintf('%02d:00', $hour);
            }
        }

        return ['date' => $dateFormatted, 'slots' => $slots];
    }

    private function getServices(): array
    {
        $services = DichVu::where('trang_thai', DichVu::STATUS_KINH_DOANH)
            ->orderBy('ten')
            ->get();

        return [
            'services' => $services->map(fn ($s) => [
                'id' => (string) $s->id,
                'ten' => $s->ten,
                'mo_ta' => $s->mo_ta ?? null,
                'gia' => $s->gia ?? null,
            ])->values()->all(),
        ];
    }

    private function bookAppointment(array $args, KhachHang $user): array
    {
        $thuCungId = isset($args['thu_cung_id']) ? (int) $args['thu_cung_id'] : null;
        $ngayGioStr = $args['ngay_gio'] ?? null;
        $dichVuId = isset($args['dich_vu_id']) ? (int) $args['dich_vu_id'] : null;
        $ghiChu = $args['ghi_chu'] ?? null;

        if (! $thuCungId || ! $ngayGioStr || ! $dichVuId) {
            return ['error' => 'Thiếu thông tin bắt buộc (thu_cung_id, ngay_gio, dich_vu_id)'];
        }

        $pet = ThuCung::where('id', $thuCungId)
            ->where('khach_hang_id', $user->id)
            ->first();

        // Fallback: model passed pet name instead of ID
        if (! $pet && isset($args['thu_cung_id'])) {
            $pet = ThuCung::where('khach_hang_id', $user->id)
                ->whereRaw('LOWER(ten_thu_cung) = ?', [mb_strtolower($args['thu_cung_id'])])
                ->first();
        }

        if (! $pet) {
            return ['error' => 'Thú cưng không hợp lệ'];
        }

        $dichVu = DichVu::where('id', $dichVuId)
            ->where('trang_thai', DichVu::STATUS_KINH_DOANH)
            ->first();

        if (! $dichVu) {
            return ['error' => 'Dịch vụ không tồn tại'];
        }

        $ngayGio = Carbon::parse($ngayGioStr);

        if ($ngayGio->lt(Carbon::now())) {
            return ['error' => 'Thời gian đã qua, vui lòng chọn thời gian trong tương lai'];
        }

        $dateStr = $ngayGio->toDateString();
        $hour = (int) $ngayGio->format('H');

        try {
            $lichHen = DB::transaction(function () use ($user, $pet, $dichVu, $ngayGio, $dateStr, $hour, $ghiChu) {
                $shifts = LichLamViec::where('ngay_lam', $dateStr)
                    ->whereHas('nhanVien', fn ($q) => $q->where('vai_tro', 'bac_si'))
                    ->get();

                $capacity = $shifts->filter(function ($shift) use ($hour) {
                    if ($shift->thoi_gian_truc === LichLamViec::CA_SANG) {
                        return $hour >= 8 && $hour <= 15;
                    }
                    if ($shift->thoi_gian_truc === LichLamViec::CA_CHIEU) {
                        return $hour >= 13 && $hour <= 16;
                    }
                    return false;
                })->count();

                if ($capacity === 0) {
                    throw new \RuntimeException('Phòng khám không có lịch làm việc cho ngày này');
                }

                $booked = LichHen::whereIn('trang_thai', ['confirmed', 'in-progress'])
                    ->where('ngay_gio', '>=', $ngayGio->copy()->subMinutes(59)->format('Y-m-d H:i:s'))
                    ->where('ngay_gio', '<', $ngayGio->copy()->addMinutes(60)->format('Y-m-d H:i:s'))
                    ->whereDate('ngay_gio', $dateStr)
                    ->lockForUpdate()
                    ->count();

                if ($booked >= $capacity) {
                    throw new \RuntimeException('Khung giờ đã hết chỗ, vui lòng chọn giờ khác');
                }

                $petConflict = LichHen::whereIn('trang_thai', ['confirmed', 'in-progress'])
                    ->where('thu_cung_id', $pet->id)
                    ->where('ngay_gio', '>=', $ngayGio->copy()->subMinutes(59)->format('Y-m-d H:i:s'))
                    ->where('ngay_gio', '<', $ngayGio->copy()->addMinutes(60)->format('Y-m-d H:i:s'))
                    ->whereDate('ngay_gio', $dateStr)
                    ->lockForUpdate()
                    ->exists();

                if ($petConflict) {
                    throw new \RuntimeException('Thú cưng đã có lịch hẹn trong khung giờ này');
                }

                return LichHen::create([
                    'khach_hang_id' => $user->id,
                    'thu_cung_id' => $pet->id,
                    'dich_vu_id' => $dichVu->id,
                    'ngay_gio' => $ngayGio->format('Y-m-d H:i:s'),
                    'ghi_chu' => $ghiChu,
                    'trang_thai' => 'confirmed',
                    'nguon_goc' => 'online',
                ]);
            });

            return [
                'success' => true,
                'appointment' => [
                    'id' => $lichHen->id,
                    'ngay_gio' => $ngayGio->format('d/m/Y H:i'),
                    'thu_cung' => $pet->ten_thu_cung,
                    'dich_vu' => $dichVu->ten,
                ],
            ];
        } catch (\RuntimeException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
