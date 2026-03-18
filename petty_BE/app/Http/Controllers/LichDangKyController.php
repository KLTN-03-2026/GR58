<?php

namespace App\Http\Controllers;

use App\Models\LichDangKy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LichDangKyController extends Controller
{
    /**
     * Display a listing of the resource.
     * Nếu là nhân viên: chỉ xem lịch của chính họ
     * Nếu là admin: xem tất cả lịch
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $query = LichDangKy::with([
                'nhanVien',
                'admin',
                'lichLamViec',
                'khachHang',
                'thuCung',
                'dichVu'
            ]);

            // Nếu là nhân viên, chỉ lấy lịch của chính họ
            if ($user instanceof \App\Models\NhanVien) {
                $query->where('nhan_vien_id', $user->id);
            }
            // Nếu là Admin, có thể xem tất cả hoặc filter theo nhan_vien_id
            elseif ($user instanceof \App\Models\Admin) {
                // Admin có thể filter theo nhan_vien_id nếu muốn
                if ($request->has('nhan_vien_id')) {
                    $query->where('nhan_vien_id', $request->nhan_vien_id);
                }
            }
            // Nếu không phải nhân viên hay admin (có thể là khách hàng)
            else {
                // Khách hàng chỉ xem lịch của chính họ
                if ($user && method_exists($user, 'getKey')) {
                    $query->where('khach_hang_id', $user->id);
                }
            }

            // Filter by status
            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            // Filter by date
            if ($request->has('ngay')) {
                $query->theoNgay($request->ngay);
            }

            // Filter by date range
            if ($request->has('tu_ngay') && $request->has('den_ngay')) {
                $query->trongKhoang($request->tu_ngay, $request->den_ngay);
            }

            // Filter by admin_id (chỉ Admin mới có thể dùng)
            if ($request->has('admin_id') && $user instanceof \App\Models\Admin) {
                $query->where('admin_id', $request->admin_id);
            }

            // Filter by khach_hang_id (chỉ Admin mới có thể dùng)
            if ($request->has('khach_hang_id') && $user instanceof \App\Models\Admin) {
                $query->where('khach_hang_id', $request->khach_hang_id);
            }

            // Filter by lich_lam_viec_id
            if ($request->has('lich_lam_viec_id')) {
                $query->where('lich_lam_viec_id', $request->lich_lam_viec_id);
            }

            $query->orderBy('ngay_gio', $request->input('sort', 'desc'));

            $lichDangKys = $query->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách lịch đăng ký thành công',
                'data' => $lichDangKys,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách lịch đăng ký: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ngay_gio' => 'required|date_format:Y-m-d H:i:s|after:now',
                'ghi_chu' => 'nullable|string',
                'trang_thai' => 'nullable|string|in:chua_xac_nhan,da_xac_nhan,tu_choi',
                'nhan_vien_id' => 'nullable|exists:nhan_viens,id',
                'admin_id' => 'nullable|exists:admins,id',
                'lich_lam_viec_id' => 'nullable|exists:lich_lam_viecs,id',
                'khach_hang_id' => 'required|exists:khach_hangs,id',
                'thu_cung_id' => 'nullable|exists:thu_cungs,id',
                'dich_vu_id' => 'nullable|exists:dich_vus,id',
            ], [
                'ngay_gio.required' => 'Ngày giờ là bắt buộc',
                'ngay_gio.date_format' => 'Ngày giờ phải có định dạng: Y-m-d H:i:s (ví dụ: 2025-12-15 14:30:00)',
                'ngay_gio.after' => 'Ngày giờ phải là trong tương lai',
                'khach_hang_id.required' => 'Khách hàng là bắt buộc',
                'khach_hang_id.exists' => 'Khách hàng không tồn tại',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $lichDangKy = LichDangKy::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Tạo lịch đăng ký thành công',
                'data' => $lichDangKy->load([
                    'nhanVien',
                    'admin',
                    'lichLamViec',
                    'khachHang',
                    'thuCung',
                    'dichVu'
                ]),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo lịch đăng ký: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(LichDangKy $lichDangKy)
    {
        try {
            $lichDangKy->load([
                'nhanVien',
                'admin',
                'lichLamViec',
                'khachHang',
                'thuCung',
                'dichVu'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lấy thông tin lịch đăng ký thành công',
                'data' => $lichDangKy,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy thông tin lịch đăng ký: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LichDangKy $lichDangKy)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ngay_gio' => 'nullable|date_format:Y-m-d H:i:s',
                'ghi_chu' => 'nullable|string',
                'trang_thai' => 'nullable|string|in:chua_xac_nhan,da_xac_nhan,tu_choi',
                'nhan_vien_id' => 'nullable|exists:nhan_viens,id',
                'admin_id' => 'nullable|exists:admins,id',
                'lich_lam_viec_id' => 'nullable|exists:lich_lam_viecs,id',
                'khach_hang_id' => 'nullable|exists:khach_hangs,id',
                'thu_cung_id' => 'nullable|exists:thu_cungs,id',
                'dich_vu_id' => 'nullable|exists:dich_vus,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $lichDangKy->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật lịch đăng ký thành công',
                'data' => $lichDangKy->load([
                    'nhanVien',
                    'admin',
                    'lichLamViec',
                    'khachHang',
                    'thuCung',
                    'dichVu'
                ]),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật lịch đăng ký: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LichDangKy $lichDangKy)
    {
        try {
            $lichDangKy->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa lịch đăng ký thành công',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa lịch đăng ký: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Xác nhận lịch đăng ký
     */
    public function xacNhan(Request $request, LichDangKy $lichDangKy)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nhan_vien_id' => 'nullable|exists:nhan_viens,id',
                'admin_id' => 'nullable|exists:admins,id',
                'lich_lam_viec_id' => 'nullable|exists:lich_lam_viecs,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $lichDangKy->update([
                'trang_thai' => 'da_xac_nhan',
                'nhan_vien_id' => $request->nhan_vien_id ?? $lichDangKy->nhan_vien_id,
                'admin_id' => $request->admin_id ?? $lichDangKy->admin_id,
                'lich_lam_viec_id' => $request->lich_lam_viec_id ?? $lichDangKy->lich_lam_viec_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Xác nhận lịch đăng ký thành công',
                'data' => $lichDangKy->load([
                    'nhanVien',
                    'admin',
                    'lichLamViec',
                    'khachHang',
                    'thuCung',
                    'dichVu'
                ]),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xác nhận lịch đăng ký: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Từ chối lịch đăng ký
     */
    public function tuChoi(Request $request, LichDangKy $lichDangKy)
    {
        try {
            $validator = Validator::make($request->all(), [
                'admin_id' => 'nullable|exists:admins,id',
                'ghi_chu' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $lichDangKy->update([
                'trang_thai' => 'tu_choi',
                'admin_id' => $request->admin_id ?? $lichDangKy->admin_id,
                'ghi_chu' => $request->ghi_chu ?? $lichDangKy->ghi_chu,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Từ chối lịch đăng ký thành công',
                'data' => $lichDangKy->load([
                    'nhanVien',
                    'admin',
                    'lichLamViec',
                    'khachHang',
                    'thuCung',
                    'dichVu'
                ]),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi từ chối lịch đăng ký: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get statuses
     */
    public function getStatuses()
    {
        return response()->json([
            'success' => true,
            'data' => LichDangKy::TRANG_THAI,
        ], 200);
    }

    /**
     * Lấy danh sách lịch chưa xác nhận
     * Hiển thị: tên nhân viên, thời gian, ghi chú, trạng thái
     * Nếu là nhân viên: chỉ xem lịch của chính họ
     * Nếu là admin: xem tất cả lịch
     */
    public function chuaXacNhan(Request $request)
    {
        try {
            $user = $request->user();

            $query = LichDangKy::chuaXacNhan()
                ->with([
                    'nhanVien:id,full_name,email', // Lấy tên nhân viên (full_name chứ không phải ho_ten)
                    'admin:id,ho_ten,email',
                    'lichLamViec',
                    'khachHang',
                    'thuCung',
                    'dichVu'
                ])
                ->orderBy('ngay_gio', 'asc');

            // Nếu là nhân viên, chỉ lấy lịch của chính họ
            if ($user instanceof \App\Models\NhanVien) {
                $query->where('nhan_vien_id', $user->id);
            }
            // Nếu là Admin, có thể filter theo nhan_vien_id
            elseif ($user instanceof \App\Models\Admin) {
                if ($request->has('nhan_vien_id')) {
                    $query->where('nhan_vien_id', $request->nhan_vien_id);
                }
            }

            // Filter by date
            if ($request->has('ngay')) {
                $query->theoNgay($request->ngay);
            }

            // Filter by date range
            if ($request->has('tu_ngay') && $request->has('den_ngay')) {
                $query->trongKhoang($request->tu_ngay, $request->den_ngay);
            }

            $lichDangKys = $query->paginate($request->input('per_page', 15));

            // Format dữ liệu hiển thị
            $lichDangKys->getCollection()->transform(function ($item) {
                return [
                    'id' => $item->id,
                    'ngay_gio' => $item->ngay_gio->format('Y-m-d H:i:s'),
                    'ghi_chu' => $item->ghi_chu,
                    'trang_thai' => $item->trang_thai,
                    'ten_nhan_vien' => $item->nhanVien?->full_name ?? 'N/A', // Thay đổi từ ho_ten -> full_name
                    'email_nhan_vien' => $item->nhanVien?->email ?? 'N/A',
                    'nhan_vien_id' => $item->nhan_vien_id,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách lịch chưa xác nhận thành công',
                'data' => $lichDangKys,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy danh sách lịch đã xác nhận
     * Có thể filter theo nhan_vien_id
     * Nếu là nhân viên: chỉ xem lịch của chính họ
     * Nếu là admin: xem tất cả lịch
     */
    public function daXacNhan(Request $request)
    {
        try {
            $user = $request->user();

            $query = LichDangKy::daXacNhan()
                ->with([
                    'nhanVien',
                    'admin',
                    'lichLamViec',
                    'khachHang',
                    'thuCung',
                    'dichVu'
                ])
                ->orderBy('ngay_gio', 'asc');

            // Nếu là nhân viên, chỉ lấy lịch của chính họ
            if ($user instanceof \App\Models\NhanVien) {
                $query->where('nhan_vien_id', $user->id);
            }
            // Nếu là Admin, có thể filter theo nhan_vien_id
            elseif ($user instanceof \App\Models\Admin) {
                if ($request->has('nhan_vien_id')) {
                    $query->where('nhan_vien_id', $request->nhan_vien_id);
                }
            }

            // Filter by date
            if ($request->has('ngay')) {
                $query->theoNgay($request->ngay);
            }

            // Filter by date range
            if ($request->has('tu_ngay') && $request->has('den_ngay')) {
                $query->trongKhoang($request->tu_ngay, $request->den_ngay);
            }

            $lichDangKys = $query->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách lịch đã xác nhận thành công',
                'data' => $lichDangKys,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy danh sách lịch từ chối
     * Có thể filter theo nhan_vien_id
     * Nếu là nhân viên: chỉ xem lịch của chính họ
     * Nếu là admin: xem tất cả lịch
     */
    public function danhSachTuChoi(Request $request)
    {
        try {
            $user = $request->user();

            $query = LichDangKy::tuChoi()
                ->with([
                    'nhanVien',
                    'admin',
                    'lichLamViec',
                    'khachHang',
                    'thuCung',
                    'dichVu'
                ])
                ->orderBy('ngay_gio', 'desc');

            // Nếu là nhân viên, chỉ lấy lịch của chính họ
            if ($user instanceof \App\Models\NhanVien) {
                $query->where('nhan_vien_id', $user->id);
            }
            // Nếu là Admin, có thể filter theo nhan_vien_id
            elseif ($user instanceof \App\Models\Admin) {
                if ($request->has('nhan_vien_id')) {
                    $query->where('nhan_vien_id', $request->nhan_vien_id);
                }
            }

            // Filter by date
            if ($request->has('ngay')) {
                $query->theoNgay($request->ngay);
            }

            // Filter by date range
            if ($request->has('tu_ngay') && $request->has('den_ngay')) {
                $query->trongKhoang($request->tu_ngay, $request->den_ngay);
            }

            $lichDangKys = $query->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách lịch từ chối thành công',
                'data' => $lichDangKys,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Nhân viên tự đăng ký lịch làm việc
     * Chỉ cần: ngay_gio và ghi_chu
     */
    public function dangKyNhanVien(Request $request)
    {
        try {
            $user = $request->user();

            // Kiểm tra xem user có phải là nhân viên không
            if (!$user instanceof \App\Models\NhanVien) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ nhân viên mới có thể đăng ký lịch làm việc',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'ngay_gio' => 'required|date_format:Y-m-d H:i:s|after:now',
                'ghi_chu' => 'nullable|string',
            ], [
                'ngay_gio.required' => 'Ngày giờ là bắt buộc',
                'ngay_gio.date_format' => 'Ngày giờ phải có định dạng: Y-m-d H:i:s (ví dụ: 2025-12-15 14:30:00)',
                'ngay_gio.after' => 'Ngày giờ phải là trong tương lai',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Tạo bản ghi lịch đăng ký với chỉ 2 trường bắt buộc
            $lichDangKy = LichDangKy::create([
                'ngay_gio' => $request->ngay_gio,
                'ghi_chu' => $request->ghi_chu,
                'nhan_vien_id' => $user->id,
                'trang_thai' => 'chua_xac_nhan', // Trạng thái chưa xác nhận, chờ admin duyệt
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký lịch làm việc thành công, chờ xác nhận',
                'data' => $lichDangKy->load([
                    'nhanVien',
                    'admin',
                    'lichLamViec',
                    'khachHang',
                    'thuCung',
                    'dichVu'
                ]),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi đăng ký lịch: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy danh sách lịch đăng ký của nhân viên đang đăng nhập
     * Hoặc Admin xem lịch của nhân viên cụ thể (truyền nhan_vien_id)
     */
    public function lichCuaToi(Request $request)
    {
        try {
            $user = $request->user();

            // Kiểm tra xem user có phải là nhân viên hoặc admin không
            if (!($user instanceof \App\Models\NhanVien) && !($user instanceof \App\Models\Admin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ nhân viên và quản trị viên mới có thể xem lịch',
                ], 403);
            }

            // Nếu là Admin, phải truyền nhan_vien_id
            if ($user instanceof \App\Models\Admin) {
                if (!$request->has('nhan_vien_id')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Admin phải cung cấp nhan_vien_id để xem lịch nhân viên',
                    ], 400);
                }
                $nhanVienId = $request->get('nhan_vien_id');
            } else {
                // Nếu là NhanVien, lấy lịch của chính họ
                $nhanVienId = $user->id;
            }

            $query = LichDangKy::where('nhan_vien_id', $nhanVienId)
                ->with([
                    'nhanVien',
                    'admin',
                    'lichLamViec',
                    'khachHang',
                    'thuCung',
                    'dichVu'
                ]);

            // Filter by status
            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            // Filter by date
            if ($request->has('ngay')) {
                $query->theoNgay($request->ngay);
            }

            $query->orderBy('ngay_gio', 'desc');

            $lichDangKys = $query->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách lịch thành công',
                'data' => $lichDangKys,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hiển thị ca đã đăng ký của nhân viên
     * Hoặc Admin xem ca đã xác nhận của nhân viên cụ thể (truyền nhan_vien_id)
     */
    public function caDaXacNhanCuaToi(Request $request)
    {
        try {
            $user = $request->user();

            // Kiểm tra xem user có phải là nhân viên hoặc admin không
            if (!($user instanceof \App\Models\NhanVien) && !($user instanceof \App\Models\Admin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ nhân viên và quản trị viên mới có thể xem ca đã đăng ký',
                ], 403);
            }

            // Nếu là Admin, phải truyền nhan_vien_id
            if ($user instanceof \App\Models\Admin) {
                if (!$request->has('nhan_vien_id')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Admin phải cung cấp nhan_vien_id để xem ca đã xác nhận',
                    ], 400);
                }
                $nhanVienId = $request->get('nhan_vien_id');
            } else {
                // Nếu là NhanVien, lấy lịch của chính họ
                $nhanVienId = $user->id;
            }

            $query = LichDangKy::where('nhan_vien_id', $nhanVienId)
                ->where('trang_thai', 'da_xac_nhan')
                ->with([
                    'nhanVien',
                    'admin',
                    'lichLamViec',
                    'khachHang',
                    'thuCung',
                    'dichVu'
                ]);

            // Filter by date
            if ($request->has('ngay')) {
                $query->theoNgay($request->ngay);
            }

            // Filter by date range
            if ($request->has('tu_ngay') && $request->has('den_ngay')) {
                $query->trongKhoang($request->tu_ngay, $request->den_ngay);
            }

            $query->orderBy('ngay_gio', 'asc');

            $caDaXacNhan = $query->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách ca đã đăng ký thành công',
                'data' => $caDaXacNhan,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách ca: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Đổi trạng thái lịch đăng ký
     * Staff only - có thể đổi sang bất kỳ trạng thái nào
     */
    public function doiTrangThai(Request $request, LichDangKy $lichDangKy)
    {
        try {
            $validator = Validator::make($request->all(), [
                'trang_thai' => 'required|string|in:chua_xac_nhan,da_xac_nhan,tu_choi',
                'admin_id' => 'nullable|exists:admins,id',
                'ghi_chu' => 'nullable|string',
            ], [
                'trang_thai.required' => 'Trạng thái là bắt buộc',
                'trang_thai.in' => 'Trạng thái không hợp lệ. Chỉ chấp nhận: chua_xac_nhan, da_xac_nhan, tu_choi',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $trangThaiCu = $lichDangKy->trang_thai;

            // Cập nhật trạng thái
            $updateData = [
                'trang_thai' => $request->trang_thai,
            ];

            // Nếu gửi admin_id hoặc ghi_chu, cập nhật
            if ($request->has('admin_id')) {
                $updateData['admin_id'] = $request->admin_id;
            }
            if ($request->has('ghi_chu')) {
                $updateData['ghi_chu'] = $request->ghi_chu;
            }

            $lichDangKy->update($updateData);

            return response()->json([
                'success' => true,
                'message' => "Đổi trạng thái từ '{$trangThaiCu}' sang '{$request->trang_thai}' thành công",
                'data' => [
                    'id' => $lichDangKy->id,
                    'ngay_gio' => $lichDangKy->ngay_gio->format('Y-m-d H:i:s'),
                    'ghi_chu' => $lichDangKy->ghi_chu,
                    'trang_thai_cu' => $trangThaiCu,
                    'trang_thai_moi' => $lichDangKy->trang_thai,
                    'nhan_vien_id' => $lichDangKy->nhan_vien_id,
                    'admin_id' => $lichDangKy->admin_id,
                    'updated_at' => $lichDangKy->updated_at->format('Y-m-d H:i:s'),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi đổi trạng thái: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy danh sách lịch đăng ký nhóm theo từng nhân viên
     * Hiển thị: Thông tin nhân viên và danh sách lịch đăng ký của họ
     * Staff only
     */
    public function danhSachTheoNhanVien(Request $request)
    {
        try {
            // Lấy tất cả nhân viên có lịch đăng ký
            $query = \App\Models\NhanVien::whereHas('lichDangKys');

            // Lọc theo trạng thái hoạt động của nhân viên nếu cần
            if ($request->has('trang_thai_nhan_vien')) {
                $query->where('trang_thai', $request->trang_thai_nhan_vien);
            }

            $nhanViens = $query->with(['lichDangKys' => function ($q) use ($request) {
                // Eager load các quan hệ của lịch đăng ký
                $q->with([
                    'admin:id,ho_ten,email',
                    'lichLamViec',
                    'khachHang:id,full_name,email,phone',
                    'thuCung:id,ten_thu_cung',
                    'dichVu:id,ten_dich_vu'
                ]);

                // Filter by trang_thai của lịch đăng ký
                if ($request->has('trang_thai')) {
                    $q->where('trang_thai', $request->trang_thai);
                }

                // Filter by ngày
                if ($request->has('ngay')) {
                    $q->theoNgay($request->ngay);
                }

                // Filter by khoảng thời gian
                if ($request->has('tu_ngay') && $request->has('den_ngay')) {
                    $q->trongKhoang($request->tu_ngay, $request->den_ngay);
                }

                // Sắp xếp theo ngày giờ
                $q->orderBy('ngay_gio', $request->input('sort', 'desc'));
            }])->get();

            // Format dữ liệu theo nhân viên
            $result = $nhanViens->map(function ($nhanVien) {
                return [
                    'nhan_vien' => [
                        'id' => $nhanVien->id,
                        'full_name' => $nhanVien->full_name,
                        'email' => $nhanVien->email,
                        'phone' => $nhanVien->phone,
                        'chuc_danh' => $nhanVien->chuc_danh,
                        'vai_tro' => $nhanVien->vai_tro,
                        'trang_thai' => $nhanVien->trang_thai,
                    ],
                    'thong_ke' => [
                        'tong_so_lich' => $nhanVien->lichDangKys->count(),
                        'chua_xac_nhan' => $nhanVien->lichDangKys->where('trang_thai', 'chua_xac_nhan')->count(),
                        'da_xac_nhan' => $nhanVien->lichDangKys->where('trang_thai', 'da_xac_nhan')->count(),
                        'tu_choi' => $nhanVien->lichDangKys->where('trang_thai', 'tu_choi')->count(),
                    ],
                    'danh_sach_lich' => $nhanVien->lichDangKys->map(function ($lich) {
                        return [
                            'id' => $lich->id,
                            'ngay_gio' => $lich->ngay_gio->format('Y-m-d H:i:s'),
                            'ghi_chu' => $lich->ghi_chu,
                            'trang_thai' => $lich->trang_thai,
                            'trang_thai_text' => LichDangKy::TRANG_THAI[$lich->trang_thai] ?? 'N/A',
                            'admin' => $lich->admin ? [
                                'id' => $lich->admin->id,
                                'ho_ten' => $lich->admin->ho_ten,
                                'email' => $lich->admin->email,
                            ] : null,
                            'khach_hang' => $lich->khachHang ? [
                                'id' => $lich->khachHang->id,
                                'full_name' => $lich->khachHang->full_name,
                                'email' => $lich->khachHang->email,
                                'phone' => $lich->khachHang->phone,
                            ] : null,
                            'thu_cung' => $lich->thuCung ? [
                                'id' => $lich->thuCung->id,
                                'ten_thu_cung' => $lich->thuCung->ten_thu_cung,
                            ] : null,
                            'dich_vu' => $lich->dichVu ? [
                                'id' => $lich->dichVu->id,
                                'ten_dich_vu' => $lich->dichVu->ten_dich_vu,
                            ] : null,
                            'created_at' => $lich->created_at->format('Y-m-d H:i:s'),
                            'updated_at' => $lich->updated_at->format('Y-m-d H:i:s'),
                        ];
                    }),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách lịch đăng ký theo nhân viên thành công',
                'data' => $result,
                'tong_so_nhan_vien' => $result->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách: ' . $e->getMessage(),
            ], 500);
        }
    }
}
