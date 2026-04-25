<?php

namespace App\Http\Controllers;

use App\Http\Requests\LichLamViecRequest;
use App\Models\LichLamViec;
use App\Models\Admin;
use App\Notifications\LichLamViecCreatedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class LichLamViecController extends Controller
{
    /**
     * Store a newly created schedule in storage.
     */
    public function store(LichLamViecRequest $request): JsonResponse
    {
        $data = $request->validated();

        $lich = LichLamViec::create($data);

        // Notify the assigned employee
        try {
            if ($lich->nhanVien) {
                $lich->nhanVien->notify(new LichLamViecCreatedNotification($lich));
            }
        } catch (\Throwable $e) {
            // ignore notification errors
        }

        // Notify admins (if any)
        try {
            if (class_exists(Admin::class)) {
                $admins = Admin::all();
                foreach ($admins as $admin) {
                    $admin->notify(new LichLamViecCreatedNotification($lich));
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return response()->json([
            'status' => true,
            'message' => 'Lịch làm việc đã được tạo.',
            'data' => $lich,
        ], 201);
    }

    /**
     * Display a listing of schedules. Supports optional filters:
     * - ngay_lam (YYYY-MM-DD)
     * - nhan_vien_id
     * - thoi_gian_truc (ca_sang|ca_chieu|ca_toi)
     * Returns paginated results with the assigned employee loaded.
     */
    public function index(Request $request): JsonResponse
    {
        $query = LichLamViec::with('nhanVien');

        if ($request->filled('ngay_lam')) {
            $query->whereDate('ngay_lam', $request->get('ngay_lam'));
        }

        if ($request->filled('nhan_vien_id')) {
            $query->where('nhan_vien_id', $request->get('nhan_vien_id'));
        }

        if ($request->filled('thoi_gian_truc')) {
            $query->where('thoi_gian_truc', $request->get('thoi_gian_truc'));
        }

        $perPage = (int) $request->get('per_page', 20);
        $lichs = $query->orderBy('ngay_lam', 'asc')->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $lichs,
        ]);
    }

    /**
     * Display a single schedule.
     */
    public function show(LichLamViec $lichLamViec): JsonResponse
    {
        $lich = $lichLamViec->load('nhanVien');
        return response()->json([
            'status' => true,
            'data' => $lich,
        ]);
    }

    /**
     * Lấy lịch làm việc của một bác sĩ/nhân viên cụ thể
     * Bao gồm cả lịch làm việc và lịch hẹn trong ngày
     */
    public function getScheduleByDoctor(Request $request, $nhanVienId): JsonResponse
    {
        // Validate parameters
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Lấy thông tin nhân viên
        $nhanVien = \App\Models\NhanVien::with('phanQuyen')->findOrFail($nhanVienId);

        // Lấy lịch làm việc
        $lichLamViecs = LichLamViec::where('nhan_vien_id', $nhanVienId)
            ->whereBetween('ngay_lam', [$startDate, $endDate])
            ->orderBy('ngay_lam', 'asc')
            ->orderBy('thoi_gian_truc', 'asc')
            ->get();

        // Lấy lịch hẹn
        $lichHens = \App\Models\LichHen::with(['khachHang', 'thuCung', 'dichVu'])
            ->where('nhan_vien_id', $nhanVienId)
            ->whereBetween('ngay_gio', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('ngay_gio', 'asc')
            ->get();

        // Gộp dữ liệu theo ngày
        $scheduleByDate = [];

        // Xử lý lịch làm việc
        foreach ($lichLamViecs as $lichLamViec) {
            // Convert ngay_lam to string format
            $date = $lichLamViec->ngay_lam instanceof \Carbon\Carbon
                ? $lichLamViec->ngay_lam->format('Y-m-d')
                : (string) $lichLamViec->ngay_lam;

            if (!isset($scheduleByDate[$date])) {
                $scheduleByDate[$date] = [
                    'date' => $date,
                    'lich_lam_viec' => [],
                    'lich_hen' => [],
                ];
            }
            $scheduleByDate[$date]['lich_lam_viec'][] = [
                'id' => $lichLamViec->id,
                'thoi_gian_truc' => $lichLamViec->thoi_gian_truc,
                'ghi_chu' => $lichLamViec->ghi_chu,
            ];
        }

        // Xử lý lịch hẹn
        foreach ($lichHens as $lichHen) {
            $date = \Carbon\Carbon::parse($lichHen->ngay_gio)->format('Y-m-d');
            if (!isset($scheduleByDate[$date])) {
                $scheduleByDate[$date] = [
                    'date' => $date,
                    'lich_lam_viec' => [],
                    'lich_hen' => [],
                ];
            }
            $scheduleByDate[$date]['lich_hen'][] = [
                'id' => $lichHen->id,
                'ngay_gio' => $lichHen->ngay_gio,
                'trang_thai' => $lichHen->trang_thai,
                'khach_hang' => $lichHen->khachHang ? $lichHen->khachHang->full_name : null,
                'thu_cung' => $lichHen->thuCung ? $lichHen->thuCung->ten_thu_cung : null,
                'dich_vu' => $lichHen->dichVu ? $lichHen->dichVu->ten : null,
                'ghi_chu' => $lichHen->ghi_chu,
            ];
        }

        // Sắp xếp theo ngày
        ksort($scheduleByDate);

        return response()->json([
            'status' => true,
            'data' => [
                'nhan_vien' => [
                    'id' => $nhanVien->id,
                    'full_name' => $nhanVien->full_name,
                    'email' => $nhanVien->email,
                    'vai_tro' => $nhanVien->vai_tro,
                    'chuc_danh' => $nhanVien->chuc_danh,
                ],
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'schedule' => array_values($scheduleByDate),
                'statistics' => [
                    'total_work_days' => count($lichLamViecs),
                    'total_appointments' => count($lichHens),
                ],
            ],
        ]);
    }

    /**
     * Lấy danh sách tất cả bác sĩ/nhân viên với lịch làm việc trong ngày hôm nay
     */
    public function getTodaySchedule(Request $request): JsonResponse
    {
        $today = $request->get('date', now()->format('Y-m-d'));

        // Lấy tất cả lịch làm việc trong ngày
        $lichLamViecs = LichLamViec::with('nhanVien.phanQuyen')
            ->whereDate('ngay_lam', $today)
            ->orderBy('thoi_gian_truc', 'asc')
            ->get();

        // Nhóm theo nhân viên
        $scheduleByEmployee = [];
        foreach ($lichLamViecs as $lich) {
            $nhanVienId = $lich->nhan_vien_id;
            if (!isset($scheduleByEmployee[$nhanVienId])) {
                $scheduleByEmployee[$nhanVienId] = [
                    'nhan_vien' => [
                        'id' => $lich->nhanVien->id,
                        'full_name' => $lich->nhanVien->full_name,
                        'email' => $lich->nhanVien->email,
                        'vai_tro' => $lich->nhanVien->vai_tro,
                        'chuc_danh' => $lich->nhanVien->chuc_danh,
                    ],
                    'lich_lam_viec' => [],
                    'lich_hen_count' => 0,
                ];
            }
            $scheduleByEmployee[$nhanVienId]['lich_lam_viec'][] = [
                'id' => $lich->id,
                'thoi_gian_truc' => $lich->thoi_gian_truc,
                'ghi_chu' => $lich->ghi_chu,
            ];

            // Đếm số lịch hẹn
            $lichHenCount = \App\Models\LichHen::where('nhan_vien_id', $nhanVienId)
                ->whereDate('ngay_gio', $today)
                ->count();
            $scheduleByEmployee[$nhanVienId]['lich_hen_count'] = $lichHenCount;
        }

        return response()->json([
            'status' => true,
            'data' => [
                'date' => $today,
                'employees' => array_values($scheduleByEmployee),
                'total_employees' => count($scheduleByEmployee),
            ],
        ]);
    }

    /**
     * Lấy lịch làm việc và lịch hẹn của bác sĩ đang đăng nhập
     * Endpoint cho bác sĩ xem lịch của chính mình
     */
    public function getMySchedule(Request $request): JsonResponse
    {
        // Lấy thông tin user đang đăng nhập
        $user = $request->user();

        // Kiểm tra xem user có phải là nhân viên hoặc admin không
        if (!($user instanceof \App\Models\NhanVien) && !($user instanceof \App\Models\Admin)) {
            return response()->json([
                'status' => false,
                'message' => 'Chỉ nhân viên và quản trị viên mới có thể xem lịch làm việc.',
            ], 403);
        }

        // Nếu là Admin, lấy nhan_vien_id từ query parameter
        if ($user instanceof \App\Models\Admin) {
            if (!$request->has('nhan_vien_id')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Admin phải cung cấp nhan_vien_id để xem lịch.',
                ], 400);
            }
            $nhanVienId = $request->get('nhan_vien_id');
        } else {
            // Nếu là NhanVien, lấy lịch của chính họ
            $nhanVienId = $user->id;
        }

        // Validate parameters
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Lấy lịch làm việc của nhân viên
        $lichLamViecs = LichLamViec::where('nhan_vien_id', $nhanVienId)
            ->whereBetween('ngay_lam', [$startDate, $endDate])
            ->orderBy('ngay_lam', 'asc')
            ->orderBy('thoi_gian_truc', 'asc')
            ->get();

        // Lấy lịch hẹn của nhân viên
        $lichHens = \App\Models\LichHen::with(['khachHang', 'thuCung', 'dichVu'])
            ->where('nhan_vien_id', $nhanVienId)
            ->whereBetween('ngay_gio', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('ngay_gio', 'asc')
            ->get();

        // Gộp dữ liệu theo ngày
        $scheduleByDate = [];

        // Xử lý lịch làm việc
        foreach ($lichLamViecs as $lichLamViec) {
            // Convert ngay_lam to string format
            $date = $lichLamViec->ngay_lam instanceof \Carbon\Carbon
                ? $lichLamViec->ngay_lam->format('Y-m-d')
                : (string) $lichLamViec->ngay_lam;

            if (!isset($scheduleByDate[$date])) {
                $scheduleByDate[$date] = [
                    'date' => $date,
                    'lich_lam_viec' => [],
                    'lich_hen' => [],
                ];
            }
            $scheduleByDate[$date]['lich_lam_viec'][] = [
                'id' => $lichLamViec->id,
                'thoi_gian_truc' => $lichLamViec->thoi_gian_truc,
                'ghi_chu' => $lichLamViec->ghi_chu,
            ];
        }

        // Xử lý lịch hẹn
        foreach ($lichHens as $lichHen) {
            $date = \Carbon\Carbon::parse($lichHen->ngay_gio)->format('Y-m-d');
            if (!isset($scheduleByDate[$date])) {
                $scheduleByDate[$date] = [
                    'date' => $date,
                    'lich_lam_viec' => [],
                    'lich_hen' => [],
                ];
            }
            $scheduleByDate[$date]['lich_hen'][] = [
                'id' => $lichHen->id,
                'ngay_gio' => $lichHen->ngay_gio,
                'trang_thai' => $lichHen->trang_thai,
                'khach_hang' => $lichHen->khachHang ? $lichHen->khachHang->full_name : null,
                'thu_cung' => $lichHen->thuCung ? $lichHen->thuCung->ten_thu_cung : null,
                'dich_vu' => $lichHen->dichVu ? $lichHen->dichVu->ten : null,
                'ghi_chu' => $lichHen->ghi_chu,
            ];
        }

        // Sắp xếp theo ngày
        ksort($scheduleByDate);

        // Lấy thông tin nhân viên
        $nhanVien = \App\Models\NhanVien::find($nhanVienId);

        return response()->json([
            'status' => true,
            'data' => [
                'nhan_vien' => $nhanVien ? [
                    'id' => $nhanVien->id,
                    'full_name' => $nhanVien->full_name,
                    'email' => $nhanVien->email,
                    'vai_tro' => $nhanVien->vai_tro,
                    'chuc_danh' => $nhanVien->chuc_danh,
                ] : null,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'schedule' => array_values($scheduleByDate),
                'statistics' => [
                    'total_work_days' => count($lichLamViecs),
                    'total_appointments' => count($lichHens),
                ],
            ],
        ]);
    }

    /**
     * Lấy lịch hôm nay của bác sĩ đang đăng nhập
     * Endpoint đơn giản cho bác sĩ xem lịch hôm nay
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'year'  => ['required', 'integer', 'min:2020', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'force' => ['nullable', 'boolean'],
        ]);

        $year  = (int) $request->year;
        $month = (int) $request->month;
        $force = (bool) ($request->force ?? false);

        $existing = LichLamViec::whereYear('ngay_lam', $year)
            ->whereMonth('ngay_lam', $month)
            ->exists();

        if ($existing && ! $force) {
            return response()->json([
                'status'  => false,
                'message' => 'Lịch tháng này đã tồn tại. Dùng force: true để tạo lại.',
            ], 422);
        }

        $params = [$year, $month];
        if ($force) {
            $params[] = '--force';
        }

        Artisan::call('lich-lam-viec:generate', array_filter([
            'year'    => $year,
            'month'   => $month,
            '--force' => $force ?: null,
        ]));

        $count = LichLamViec::whereYear('ngay_lam', $year)
            ->whereMonth('ngay_lam', $month)
            ->count();

        return response()->json([
            'status'  => true,
            'message' => "Đã sinh lịch tháng {$month}/{$year} thành công",
            'data'    => ['so_ban_ghi' => $count],
        ]);
    }

    public function getMyTodaySchedule(Request $request): JsonResponse
    {
        // Lấy thông tin user đang đăng nhập
        $user = $request->user();

        // Kiểm tra xem user có phải là nhân viên hoặc admin không
        if (!($user instanceof \App\Models\NhanVien) && !($user instanceof \App\Models\Admin)) {
            return response()->json([
                'status' => false,
                'message' => 'Chỉ nhân viên và quản trị viên mới có thể xem lịch làm việc.',
            ], 403);
        }

        // Nếu là Admin, lấy nhan_vien_id từ query parameter
        if ($user instanceof \App\Models\Admin) {
            if (!$request->has('nhan_vien_id')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Admin phải cung cấp nhan_vien_id để xem lịch.',
                ], 400);
            }
            $nhanVienId = $request->get('nhan_vien_id');
        } else {
            // Nếu là NhanVien, lấy lịch của chính họ
            $nhanVienId = $user->id;
        }

        $today = now()->format('Y-m-d');

        // Lấy lịch làm việc hôm nay
        $lichLamViecs = LichLamViec::where('nhan_vien_id', $nhanVienId)
            ->whereDate('ngay_lam', $today)
            ->orderBy('thoi_gian_truc', 'asc')
            ->get();

        // Lấy lịch hẹn hôm nay
        $lichHens = \App\Models\LichHen::with(['khachHang', 'thuCung', 'dichVu'])
            ->where('nhan_vien_id', $nhanVienId)
            ->whereDate('ngay_gio', $today)
            ->orderBy('ngay_gio', 'asc')
            ->get();

        // Format lịch làm việc
        $formattedLichLamViec = $lichLamViecs->map(function ($lich) {
            return [
                'id' => $lich->id,
                'thoi_gian_truc' => $lich->thoi_gian_truc,
                'ghi_chu' => $lich->ghi_chu,
            ];
        });

        // Format lịch hẹn
        $formattedLichHen = $lichHens->map(function ($lich) {
            return [
                'id' => $lich->id,
                'ngay_gio' => $lich->ngay_gio,
                'trang_thai' => $lich->trang_thai,
                'khach_hang' => $lich->khachHang ? $lich->khachHang->full_name : null,
                'khach_hang_phone' => $lich->khachHang ? $lich->khachHang->phone : null,
                'thu_cung' => $lich->thuCung ? $lich->thuCung->ten_thu_cung : null,
                'dich_vu' => $lich->dichVu ? $lich->dichVu->ten : null,
                'ghi_chu' => $lich->ghi_chu,
            ];
        });

        // Lấy thông tin nhân viên
        $nhanVien = \App\Models\NhanVien::find($nhanVienId);

        return response()->json([
            'status' => true,
            'data' => [
                'date' => $today,
                'nhan_vien' => $nhanVien ? [
                    'id' => $nhanVien->id,
                    'full_name' => $nhanVien->full_name,
                    'vai_tro' => $nhanVien->vai_tro,
                    'chuc_danh' => $nhanVien->chuc_danh,
                ] : null,
                'lich_lam_viec' => $formattedLichLamViec,
                'lich_hen' => $formattedLichHen,
                'statistics' => [
                    'work_shifts' => count($lichLamViecs),
                    'appointments' => count($lichHens),
                    'pending_appointments' => $lichHens->where('trang_thai', 'cho_xac_nhan')->count(),
                    'confirmed_appointments' => $lichHens->where('trang_thai', 'da_xac_nhan')->count(),
                ],
            ],
        ]);
    }
}
