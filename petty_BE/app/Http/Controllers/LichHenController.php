<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLichHenRequest;
use App\Models\LichHen;
use App\Models\LichLamViec;
use App\Models\ThuCung;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LichHenController extends Controller
{
    /**
     * Trả về danh sách slot còn trống trong ngày (8:00–16:00, bỏ 12:00 nghỉ trưa).
     * Capacity = số bác sĩ có LichLamViec cover giờ đó.
     * ca_sang: 8:00–16:59 | ca_chieu: 13:00–16:59
     */
    public function availableSlots(Request $request): JsonResponse
    {
        $request->validate(['date' => ['required', 'date']]);

        $date = Carbon::parse($request->date)->startOfDay();

        if ($date->lt(Carbon::today())) {
            return response()->json([
                'status'  => false,
                'message' => 'Không thể xem slot của ngày đã qua',
            ], 422);
        }

        $dateStr   = $date->toDateString();
        $allShifts = LichLamViec::where('ngay_lam', $dateStr)
            ->whereHas('nhanVien', fn ($q) => $q->where('vai_tro', 'bac_si'))
            ->get();

        if ($allShifts->isEmpty()) {
            return response()->json([
                'status'  => true,
                'message' => 'Phòng khám không có lịch làm việc cho ngày này',
                'slots'   => [],
            ]);
        }

        $slots = [];
        for ($hour = 8; $hour <= 16; $hour++) {
            // Bỏ giờ 12:00 — giờ nghỉ trưa của phòng khám
            if ($hour === 12) {
                continue;
            }

            $slotTime = Carbon::parse($dateStr . ' ' . sprintf('%02d:00:00', $hour));

            // ca_sang: 08:00–16:59 (slot cuối 16:00, bệnh nhân ra 17:00)
            // ca_chieu: 13:00–16:59
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
                ->where('ngay_gio', '<',  $slotTime->copy()->addMinutes(60)->format('Y-m-d H:i:s'))
                ->whereDate('ngay_gio', $dateStr)
                ->count();

            $slots[] = [
                'time'               => sprintf('%02d:00', $hour),
                'capacity_total'     => $capacity,
                'capacity_remaining' => max(0, $capacity - $booked),
                'available'          => $booked < $capacity,
            ];
        }

        return response()->json([
            'status' => true,
            'date'   => $dateStr,
            'slots'  => $slots,
        ]);
    }

    /**
     * Store a newly created appointment in storage.
     * KhachHang booking → auto-confirmed + slot check inside transaction.
     */
    public function store(StoreLichHenRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (!empty($data['ngay_gio'])) {
            $data['ngay_gio'] = Carbon::parse($data['ngay_gio'])->format('Y-m-d H:i:s');
        }

        $user = $request->user();

        if ($user instanceof \App\Models\KhachHang) {
            $data['khach_hang_id'] = $user->id;
            $data['nguon_goc']     = 'online';

            $owns = ThuCung::where('id', $data['thu_cung_id'])
                ->where('khach_hang_id', $user->id)
                ->exists();

            if (! $owns) {
                throw ValidationException::withMessages([
                    'thu_cung_id' => [\Illuminate\Support\Facades\Lang::get('messages.pet_not_owner')],
                ]);
            }

            // Auto-confirm với slot check + lock để tránh race condition
            $lichHen = DB::transaction(function () use ($data) {
                $ngayGio  = Carbon::parse($data['ngay_gio']);
                $dateStr  = $ngayGio->toDateString();
                $hour     = (int) $ngayGio->format('H');

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
                    throw ValidationException::withMessages([
                        'ngay_gio' => ['Phòng khám không có lịch làm việc cho ngày này, vui lòng chọn ngày khác'],
                    ]);
                }

                $booked = LichHen::whereIn('trang_thai', ['confirmed', 'in-progress'])
                    ->where('ngay_gio', '>=', $ngayGio->copy()->subMinutes(59)->format('Y-m-d H:i:s'))
                    ->where('ngay_gio', '<',  $ngayGio->copy()->addMinutes(60)->format('Y-m-d H:i:s'))
                    ->whereDate('ngay_gio', $dateStr)
                    ->lockForUpdate()
                    ->count();

                if ($booked >= $capacity) {
                    throw ValidationException::withMessages([
                        'ngay_gio' => ['Khung giờ này đã đầy, vui lòng chọn giờ khác'],
                    ]);
                }

                $data['trang_thai'] = 'confirmed';
                return LichHen::create($data);
            });
        } elseif ($user instanceof \App\Models\Admin || $user instanceof \App\Models\NhanVien) {
            $data['nguon_goc'] = $data['nguon_goc'] ?? 'walk-in';
            if (empty($data['khach_hang_id'])) {
                throw ValidationException::withMessages([
                    'khach_hang_id' => ['Vui lòng chọn khách hàng'],
                ]);
            }
            $lichHen = LichHen::create($data);
        } else {
            $lichHen = LichHen::create($data);
        }

        $payload = new \App\Http\Resources\LichHenResource($lichHen->fresh()->load(['thuCung', 'dichVu', 'nhanVien', 'yTaCheckin', 'khachHang']));

        return response()->json([
            'status' => true,
            'data'   => $payload,
        ], 201);
    }

    /**
     * Display a listing of the authenticated customer's appointments.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = LichHen::with(['thuCung', 'dichVu', 'nhanVien', 'yTaCheckin', 'thanhToan'])
            ->where('khach_hang_id', $user->id);

        // Filter by pet name (thu_cung.ten_thu_cung)
        if ($request->filled('pet_name')) {
            $petName = $request->get('pet_name');
            $query->whereHas('thuCung', function ($q) use ($petName) {
                $q->where('ten_thu_cung', 'like', '%' . $petName . '%');
            });
        }

        // Filter by service id or service name
        if ($request->filled('dich_vu_id')) {
            $query->where('dich_vu_id', $request->get('dich_vu_id'));
        } elseif ($request->filled('dich_vu_name')) {
            $dvName = $request->get('dich_vu_name');
            $query->whereHas('dichVu', function ($q) use ($dvName) {
                $q->where('ten', 'like', '%' . $dvName . '%');
            });
        }

        // Filter by trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->get('trang_thai'));
        }

        // Filter by time range: from_date and to_date (accepts date or datetime)
        try {
            $from = $request->filled('from_date') ? Carbon::parse($request->get('from_date')) : null;
            $to = $request->filled('to_date') ? Carbon::parse($request->get('to_date')) : null;
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => \Illuminate\Support\Facades\Lang::get('messages.invalid_date_format'),
            ], 422);
        }

        if ($from && $to) {
            // include whole day for to date if only date provided
            $query->whereBetween('ngay_gio', [$from->format('Y-m-d H:i:s'), $to->format('Y-m-d H:i:s')]);
        } elseif ($from) {
            $query->where('ngay_gio', '>=', $from->format('Y-m-d H:i:s'));
        } elseif ($to) {
            $query->where('ngay_gio', '<=', $to->format('Y-m-d H:i:s'));
        }

        $query->orderBy('ngay_gio', 'asc');

        // pagination or full list
        if ($request->has('per_page')) {
            $perPage = (int) $request->get('per_page', 15);
            $data = $query->paginate($perPage);
        } else {
            $data = $query->get();
        }

        $payload = \App\Http\Resources\LichHenResource::collection($data);

        return response()->json([
            'status' => true,
            'data' => $payload,
        ]);
    }

    /**
     * Display a listing of ALL appointments (for staff/admin only)
     */
    public function indexAll(Request $request): JsonResponse
    {
        $query = LichHen::with(['thuCung', 'dichVu', 'nhanVien', 'yTaCheckin', 'thanhToan', 'khachHang']);

        // Filter by customer
        if ($request->filled('khach_hang_id')) {
            $query->where('khach_hang_id', $request->get('khach_hang_id'));
        }

        // Filter by pet name (thu_cung.ten_thu_cung)
        if ($request->filled('pet_name')) {
            $petName = $request->get('pet_name');
            $query->whereHas('thuCung', function ($q) use ($petName) {
                $q->where('ten_thu_cung', 'like', '%' . $petName . '%');
            });
        }

        // Filter by service id or service name
        if ($request->filled('dich_vu_id')) {
            $query->where('dich_vu_id', $request->get('dich_vu_id'));
        } elseif ($request->filled('dich_vu_name')) {
            $dvName = $request->get('dich_vu_name');
            $query->whereHas('dichVu', function ($q) use ($dvName) {
                $q->where('ten', 'like', '%' . $dvName . '%');
            });
        }

        // Filter by trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->get('trang_thai'));
        }

        // Filter by nhan_vien_id
        if ($request->filled('nhan_vien_id')) {
            $query->where('nhan_vien_id', $request->get('nhan_vien_id'));
        }

        // Filter by time range: from_date and to_date (accepts date or datetime)
        try {
            $from = $request->filled('from_date') ? Carbon::parse($request->get('from_date')) : null;
            $to = $request->filled('to_date') ? Carbon::parse($request->get('to_date')) : null;
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => \Illuminate\Support\Facades\Lang::get('messages.invalid_date_format'),
            ], 422);
        }

        if ($from && $to) {
            $query->whereBetween('ngay_gio', [$from->format('Y-m-d H:i:s'), $to->format('Y-m-d H:i:s')]);
        } elseif ($from) {
            $query->where('ngay_gio', '>=', $from->format('Y-m-d H:i:s'));
        } elseif ($to) {
            $query->where('ngay_gio', '<=', $to->format('Y-m-d H:i:s'));
        }

        $query->orderBy('ngay_gio', 'desc');

        // pagination or full list
        if ($request->has('per_page')) {
            $perPage = (int) $request->get('per_page', 15);
            $data = $query->paginate($perPage);
        } else {
            $data = $query->get();
        }

        $payload = \App\Http\Resources\LichHenResource::collection($data);

        return response()->json([
            'status' => true,
            'data' => $payload,
        ]);
    }

    /**
     * Display the specified appointment (must belong to authenticated customer, or staff can view any).
     */
    public function show(Request $request, LichHen $lichHen): JsonResponse
    {
        $user = $request->user();

        // Nếu là khách hàng, chỉ xem được lịch của mình
        if ($user instanceof \App\Models\KhachHang) {
            if ($lichHen->khach_hang_id !== $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => \Illuminate\Support\Facades\Lang::get('messages.appointment_unauthorized_view')
                ], 403);
            }
        }
        // Staff (Admin/NhanVien) có thể xem bất kỳ lịch hẹn nào

        $payload = new \App\Http\Resources\LichHenResource($lichHen->load(['thuCung', 'dichVu', 'nhanVien', 'yTaCheckin', 'thanhToan', 'khachHang']));

        return response()->json([
            'status' => true,
            'data' => $payload,
        ]);
    }

    /**
     * Update lịch hẹn (gán bác sĩ hoặc cập nhật thông tin)
     */
    public function update(\App\Http\Requests\UpdateLichHenRequest $request, LichHen $lichHen): JsonResponse
    {
        try {
            $validated = $request->validated();

            // Cập nhật các trường được phép
            if (isset($validated['nhan_vien_id'])) {
                $lichHen->nhan_vien_id = $validated['nhan_vien_id'];
            }

            if (isset($validated['trang_thai'])) {
                $lichHen->trang_thai = $validated['trang_thai'];
            }

            if (isset($validated['ghi_chu'])) {
                $lichHen->ghi_chu = $validated['ghi_chu'];
            }

            $lichHen->save();

            // Load relationships để trả lại dữ liệu đầy đủ
            $lichHen->load(['khachHang', 'thuCung', 'dichVu', 'nhanVien', 'yTaCheckin', 'thanhToan']);

            $payload = new \App\Http\Resources\LichHenResource($lichHen);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật lịch hẹn thành công',
                'data' => $payload,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi xác thực dữ liệu',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi cập nhật lịch hẹn: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Xác nhận lịch hẹn (chuyển từ pending sang confirmed)
     */
    public function confirm(Request $request, LichHen $lichHen): JsonResponse
    {
        try {
            // Kiểm tra trạng thái hiện tại
            if ($lichHen->trang_thai !== 'pending') {
                return response()->json([
                    'status' => false,
                    'message' => 'Lịch hẹn không ở trạng thái chưa xác nhận. Trạng thái hiện tại: ' . $lichHen->trang_thai,
                ], 422);
            }

            // Chuyển trạng thái sang confirmed
            $lichHen->trang_thai = 'confirmed';
            $lichHen->save();

            // Load relationships để trả lại dữ liệu đầy đủ
            $lichHen->load(['khachHang', 'thuCung', 'dichVu', 'nhanVien', 'yTaCheckin', 'thanhToan']);

            $payload = new \App\Http\Resources\LichHenResource($lichHen);

            return response()->json([
                'status' => true,
                'message' => 'Xác nhận lịch hẹn thành công',
                'data' => $payload,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi xác nhận lịch hẹn: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update only the ngay_gio (date/time) of the appointment.
     * Customer can update their own, staff can update any.
     */
    public function updateNgayGio(\App\Http\Requests\UpdateLichHenRequest $request, LichHen $lichHen): JsonResponse
    {
        $user = $request->user();

        // Nếu là khách hàng, chỉ sửa được lịch của mình
        if ($user instanceof \App\Models\KhachHang) {
            if ($lichHen->khach_hang_id !== $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => \Illuminate\Support\Facades\Lang::get('messages.appointment_unauthorized_update')
                ], 403);
            }
        }
        // Staff có thể sửa bất kỳ lịch hẹn nào

        $validated = $request->validated();

        $lichHen->ngay_gio = Carbon::parse($validated['ngay_gio'])->format('Y-m-d H:i:s');
        $lichHen->save();

        $payload = new \App\Http\Resources\LichHenResource($lichHen->fresh()->load(['thuCung', 'dichVu', 'nhanVien', 'yTaCheckin', 'khachHang']));

        return response()->json([
            'status' => true,
            'data' => $payload,
        ]);
    }

    /**
     * Remove the specified appointment.
     * Customer can delete their own, staff can delete any.
     */
    public function destroy(Request $request, LichHen $lichHen): JsonResponse
    {
        $user = $request->user();

        // Nếu là khách hàng, chỉ xóa được lịch của mình
        if ($user instanceof \App\Models\KhachHang) {
            if ($lichHen->khach_hang_id !== $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => \Illuminate\Support\Facades\Lang::get('messages.appointment_unauthorized_delete')
                ], 403);
            }
        }
        // Staff có thể xóa bất kỳ lịch hẹn nào

        $lichHen->delete();

        return response()->json([
            'status' => true,
            'message' => \Illuminate\Support\Facades\Lang::get('messages.appointment_deleted_success'),
        ]);
    }

    /**
     * Check-in lịch hẹn
     * Chỉ nhân viên có vai trò Y tá mới có thể check-in
     */
    public function checkIn(Request $request, LichHen $lichHen): JsonResponse
    {
        try {
            $user = $request->user();

            // Kiểm tra xem user có phải là nhân viên không
            if (!$user instanceof \App\Models\NhanVien) {
                return response()->json([
                    'status' => false,
                    'message' => 'Chỉ nhân viên mới có thể thực hiện check-in',
                ], 403);
            }

            // Kiểm tra vai trò của nhân viên (chỉ Y tá mới được check-in)
            // Giả sử vai_tro có các giá trị: 'bac_si', 'y_ta', 'le_tan', etc.
            if (!in_array(strtolower($user->vai_tro), ['y_ta', 'y tá', 'nurse'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Chỉ nhân viên có vai trò Y tá mới có thể thực hiện check-in',
                ], 403);
            }

            // Kiểm tra trạng thái lịch hẹn
            if (!in_array($lichHen->trang_thai, ['pending', 'confirmed'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Lịch hẹn không thể check-in. Trạng thái hiện tại: ' . $lichHen->trang_thai,
                ], 422);
            }

            // Kiểm tra xem đã check-in chưa
            if ($lichHen->thoi_gian_checkin !== null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Lịch hẹn đã được check-in lúc: ' . $lichHen->thoi_gian_checkin->format('d/m/Y H:i:s'),
                    'data' => [
                        'thoi_gian_checkin' => $lichHen->thoi_gian_checkin->format('Y-m-d H:i:s'),
                    ],
                ], 422);
            }

            // Thực hiện check-in
            $lichHen->thoi_gian_checkin = now();
            $lichHen->trang_thai = 'in-progress'; // Chuyển sang trạng thái chờ bác sĩ khám
            $lichHen->y_ta_checkin_id = $user->id; // Lưu ID y tá thực hiện check-in

            // Gán bác sĩ nếu được chọn
            if ($request->has('nhan_vien_id')) {
                $lichHen->nhan_vien_id = $request->nhan_vien_id;
            }

            $lichHen->save();

            // Load relationships để trả lại dữ liệu đầy đủ
            $lichHen->load(['khachHang', 'thuCung', 'dichVu', 'nhanVien', 'yTaCheckin', 'thanhToan']);

            $payload = new \App\Http\Resources\LichHenResource($lichHen);

            return response()->json([
                'status' => true,
                'message' => 'Check-in lịch hẹn thành công. Bệnh nhân đã sẵn sàng chờ bác sĩ khám.',
                'data' => $payload,
                'thoi_gian_checkin' => $lichHen->thoi_gian_checkin->format('Y-m-d H:i:s'),
                'y_ta_checkin' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'vai_tro' => $user->vai_tro,
                ],
                'notification' => 'Lịch hẹn đã được chuyển vào danh sách chờ khám của bác sĩ',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi check-in lịch hẹn: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Danh sách lịch hẹn cần check-in
     * Lấy các lịch hẹn có trạng thái confirmed và chưa check-in
     */
    public function lichHenChoCheckIn(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Chỉ nhân viên mới xem được
            if (!($user instanceof \App\Models\NhanVien) && !($user instanceof \App\Models\Admin)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Chỉ nhân viên mới có thể xem danh sách lịch hẹn chờ check-in',
                ], 403);
            }

            $query = LichHen::with(['khachHang', 'thuCung', 'dichVu', 'nhanVien', 'yTaCheckin'])
                ->whereIn('trang_thai', ['pending', 'confirmed'])
                ->whereNull('thoi_gian_checkin');

            // Filter theo ngày nếu có
            if ($request->filled('ngay')) {
                try {
                    $ngay = Carbon::parse($request->get('ngay'))->format('Y-m-d');
                    $query->whereDate('ngay_gio', $ngay);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Định dạng ngày không hợp lệ',
                    ], 422);
                }
            } else {
                // Mặc định lấy lịch hẹn của hôm nay
                $query->whereDate('ngay_gio', today());
            }

            // Sắp xếp theo thời gian
            $query->orderBy('ngay_gio', 'asc');

            // Pagination
            $perPage = (int) $request->get('per_page', 15);
            $data = $query->paginate($perPage);

            $payload = \App\Http\Resources\LichHenResource::collection($data);

            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách lịch hẹn chờ check-in thành công',
                'data' => $payload,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi lấy danh sách: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Danh sách lịch hẹn đã check-in
     */
    public function lichHenDaCheckIn(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Chỉ nhân viên mới xem được
            if (!($user instanceof \App\Models\NhanVien) && !($user instanceof \App\Models\Admin)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Chỉ nhân viên mới có thể xem danh sách lịch hẹn đã check-in',
                ], 403);
            }

            $query = LichHen::with(['khachHang', 'thuCung', 'dichVu', 'nhanVien', 'yTaCheckin'])
                ->whereNotNull('thoi_gian_checkin');

            // Filter theo ngày nếu có
            if ($request->filled('ngay')) {
                try {
                    $ngay = Carbon::parse($request->get('ngay'))->format('Y-m-d');
                    $query->whereDate('thoi_gian_checkin', $ngay);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Định dạng ngày không hợp lệ',
                    ], 422);
                }
            } else {
                // Mặc định lấy lịch hẹn đã check-in hôm nay
                $query->whereDate('thoi_gian_checkin', today());
            }

            // Filter theo trạng thái
            if ($request->filled('trang_thai')) {
                $query->where('trang_thai', $request->get('trang_thai'));
            }

            // Sắp xếp theo thời gian check-in
            $query->orderBy('thoi_gian_checkin', 'desc');

            // Pagination
            $perPage = (int) $request->get('per_page', 15);
            $data = $query->paginate($perPage);

            $payload = \App\Http\Resources\LichHenResource::collection($data);

            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách lịch hẹn đã check-in thành công',
                'data' => $payload,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi lấy danh sách: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Danh sách bệnh nhân chờ khám (cho Bác sĩ)
     * Lấy các lịch hẹn đã check-in nhưng chưa bắt đầu khám
     */
    public function benhNhanChoKham(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Chỉ nhân viên (Bác sĩ) mới xem được
            if (!($user instanceof \App\Models\NhanVien) && !($user instanceof \App\Models\Admin)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Chỉ bác sĩ mới có thể xem danh sách bệnh nhân chờ khám',
                ], 403);
            }

            $query = LichHen::with(['khachHang', 'thuCung', 'dichVu', 'nhanVien', 'yTaCheckin'])
                ->whereNotNull('thoi_gian_checkin') // Đã check-in
                ->where('trang_thai', 'in-progress') // Đang chờ khám
                ->whereNull('thoi_gian_bat_dau_kham'); // Chưa bắt đầu khám

            // Nếu là bác sĩ, chỉ xem bệnh nhân được phân công cho mình
            if ($user instanceof \App\Models\NhanVien) {
                // Kiểm tra vai trò (chỉ bác sĩ)
                if (in_array(strtolower($user->vai_tro), ['bac_si', 'bác sĩ', 'doctor'])) {
                    // Bác sĩ chỉ xem bệnh nhân được phân cho mình hoặc chưa phân bác sĩ
                    $query->where(function ($q) use ($user) {
                        $q->where('nhan_vien_id', $user->id)
                            ->orWhereNull('nhan_vien_id');
                    });
                }
                // Admin xem tất cả
            }

            // Filter theo ngày nếu có
            if ($request->filled('ngay')) {
                try {
                    $ngay = Carbon::parse($request->get('ngay'))->format('Y-m-d');
                    $query->whereDate('thoi_gian_checkin', $ngay);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Định dạng ngày không hợp lệ',
                    ], 422);
                }
            } else {
                // Mặc định lấy bệnh nhân chờ khám hôm nay
                $query->whereDate('thoi_gian_checkin', today());
            }

            // Sắp xếp ưu tiên online trước, sau đó theo thời gian check-in ai check-in trước khám trước
            $query->orderByRaw("CASE WHEN nguon_goc = 'online' THEN 1 ELSE 2 END")
                  ->orderBy('thoi_gian_checkin', 'asc');

            // Pagination
            $perPage = (int) $request->get('per_page', 15);
            $data = $query->paginate($perPage);

            $payload = \App\Http\Resources\LichHenResource::collection($data);

            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách bệnh nhân chờ khám thành công',
                'data' => $payload,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi lấy danh sách: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bác sĩ bắt đầu khám bệnh
     */
    public function batDauKham(Request $request, LichHen $lichHen): JsonResponse
    {
        try {
            $user = $request->user();

            // Kiểm tra xem user có phải là bác sĩ không
            if (!$user instanceof \App\Models\NhanVien) {
                return response()->json([
                    'status' => false,
                    'message' => 'Chỉ bác sĩ mới có thể bắt đầu khám',
                ], 403);
            }

            // Kiểm tra vai trò bác sĩ
            if (!in_array(strtolower($user->vai_tro), ['bac_si', 'bác sĩ', 'doctor'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Chỉ nhân viên có vai trò Bác sĩ mới có thể bắt đầu khám',
                ], 403);
            }

            // Kiểm tra trạng thái lịch hẹn
            if ($lichHen->trang_thai !== 'in-progress') {
                return response()->json([
                    'status' => false,
                    'message' => 'Lịch hẹn không thể bắt đầu khám. Trạng thái hiện tại: ' . $lichHen->trang_thai,
                ], 422);
            }

            // Kiểm tra đã check-in chưa
            if ($lichHen->thoi_gian_checkin === null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bệnh nhân chưa check-in',
                ], 422);
            }

            // Kiểm tra đã bắt đầu khám chưa
            if ($lichHen->thoi_gian_bat_dau_kham !== null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Đã bắt đầu khám lúc: ' . $lichHen->thoi_gian_bat_dau_kham->format('d/m/Y H:i:s'),
                    'data' => [
                        'thoi_gian_bat_dau_kham' => $lichHen->thoi_gian_bat_dau_kham->format('Y-m-d H:i:s'),
                        'bac_si' => $lichHen->nhanVien ? [
                            'id' => $lichHen->nhanVien->id,
                            'full_name' => $lichHen->nhanVien->full_name,
                        ] : null,
                    ],
                ], 422);
            }

            // Gán bác sĩ và bắt đầu khám
            $lichHen->nhan_vien_id = $user->id; // Gán bác sĩ đang khám
            $lichHen->thoi_gian_bat_dau_kham = now();
            $lichHen->save();

            // Load relationships
            $lichHen->load(['khachHang', 'thuCung', 'dichVu', 'nhanVien', 'thanhToan']);

            $payload = new \App\Http\Resources\LichHenResource($lichHen);

            return response()->json([
                'status' => true,
                'message' => 'Bắt đầu khám bệnh thành công',
                'data' => $payload,
                'thoi_gian_bat_dau_kham' => $lichHen->thoi_gian_bat_dau_kham->format('Y-m-d H:i:s'),
                'bac_si' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'vai_tro' => $user->vai_tro,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi bắt đầu khám: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Danh sách bệnh nhân đang khám (của bác sĩ)
     */
    public function benhNhanDangKham(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Chỉ bác sĩ mới xem được
            if (!($user instanceof \App\Models\NhanVien) && !($user instanceof \App\Models\Admin)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Chỉ bác sĩ mới có thể xem danh sách bệnh nhân đang khám',
                ], 403);
            }

            $query = LichHen::with(['khachHang', 'thuCung', 'dichVu', 'nhanVien'])
                ->whereNotNull('thoi_gian_bat_dau_kham') // Đã bắt đầu khám
                ->whereNull('thoi_gian_hoan_thanh') // Chưa hoàn thành
                ->where('trang_thai', 'in-progress');

            // Nếu là bác sĩ, chỉ xem bệnh nhân của mình
            if ($user instanceof \App\Models\NhanVien) {
                if (in_array(strtolower($user->vai_tro), ['bac_si', 'bác sĩ', 'doctor'])) {
                    $query->where('nhan_vien_id', $user->id);
                }
            }

            // Filter theo ngày
            if ($request->filled('ngay')) {
                try {
                    $ngay = Carbon::parse($request->get('ngay'))->format('Y-m-d');
                    $query->whereDate('thoi_gian_bat_dau_kham', $ngay);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Định dạng ngày không hợp lệ',
                    ], 422);
                }
            } else {
                // Mặc định lấy bệnh nhân đang khám hôm nay
                $query->whereDate('thoi_gian_bat_dau_kham', today());
            }

            // Sắp xếp theo thời gian bắt đầu khám
            $query->orderBy('thoi_gian_bat_dau_kham', 'asc');

            // Pagination
            $perPage = (int) $request->get('per_page', 15);
            $data = $query->paginate($perPage);

            $payload = \App\Http\Resources\LichHenResource::collection($data);

            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách bệnh nhân đang khám thành công',
                'data' => $payload,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi lấy danh sách: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hoàn thành khám bệnh
     */
    public function hoanThanhKham(\App\Http\Requests\UpdateLichHenRequest $request, LichHen $lichHen): JsonResponse
    {
        try {
            $user = $request->user();

            // Kiểm tra xem user có phải là bác sĩ không
            if (!$user instanceof \App\Models\NhanVien) {
                return response()->json([
                    'status' => false,
                    'message' => 'Chỉ bác sĩ mới có thể hoàn thành khám',
                ], 403);
            }

            // Kiểm tra vai trò bác sĩ
            if (!in_array(strtolower($user->vai_tro), ['bac_si', 'bác sĩ', 'doctor'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Chỉ nhân viên có vai trò Bác sĩ mới có thể hoàn thành khám',
                ], 403);
            }

            // Kiểm tra có phải bác sĩ đang khám không
            if ($lichHen->nhan_vien_id !== $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bạn không phải bác sĩ đang khám bệnh nhân này',
                ], 403);
            }

            // Kiểm tra đã bắt đầu khám chưa
            if ($lichHen->thoi_gian_bat_dau_kham === null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Chưa bắt đầu khám',
                ], 422);
            }

            // Kiểm tra đã hoàn thành chưa
            if ($lichHen->thoi_gian_hoan_thanh !== null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Đã hoàn thành khám lúc: ' . $lichHen->thoi_gian_hoan_thanh->format('d/m/Y H:i:s'),
                ], 422);
            }

            // Validation cho dữ liệu bổ sung (nếu có)
            $validated = $request->validated();

            // Hoàn thành khám
            $lichHen->thoi_gian_hoan_thanh = now();
            $lichHen->trang_thai = 'completed';

            if (isset($validated['ghi_chu'])) {
                $lichHen->ghi_chu = $validated['ghi_chu'];
            }

            if (isset($validated['huong_dan'])) {
                $lichHen->huong_dan = $validated['huong_dan'];
            }

            $lichHen->save();

            // Load relationships
            $lichHen->load(['khachHang', 'thuCung', 'dichVu', 'nhanVien', 'thanhToan']);

            $payload = new \App\Http\Resources\LichHenResource($lichHen);

            return response()->json([
                'status' => true,
                'message' => 'Hoàn thành khám bệnh thành công',
                'data' => $payload,
                'thoi_gian_hoan_thanh' => $lichHen->thoi_gian_hoan_thanh->format('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi hoàn thành khám: ' . $e->getMessage(),
            ], 500);
        }
    }
}