<?php

namespace App\Http\Controllers;

use App\Models\PhieuKham;
use Illuminate\Http\Request;

class PhieuKhamController extends Controller
{
    /**
     * Thêm mới phiếu khám
     */
    public function store(Request $request)
    {
        try {
            // Validate dữ liệu
            $validated = $request->validate([
                'lich_hen_id' => 'required|exists:lich_hens,id',
                'nhiet_do' => 'nullable|numeric|between:30,45',
                'can_nang' => 'nullable|numeric|min:0',
                'nhip_tim' => 'nullable|integer|between:30,200',
                'nhip_tho' => 'nullable|integer|between:5,50',
                'ly_do_den_kham' => 'nullable|string|max:255',
                'trieu_chung' => 'nullable|string',
                'chan_doan' => 'nullable|string',
                'ghi_chu' => 'nullable|string',
                'loai_chi_dinh' => 'required|in:chi_dinh_can_lam_sang,don_thuoc,hen_tai_kham',
            ]);

            // Lấy user đang đăng nhập
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Vui lòng đăng nhập',
                    'errors' => ['auth' => 'Chưa xác thực']
                ], 401);
            }

            // Kiểm tra xem user có phải NhanVien không
            $nhanVienId = null;

            if ($user instanceof \App\Models\NhanVien) {
                $nhanVienId = $user->id;
            } elseif ($user instanceof \App\Models\Admin) {
                // Admin phải cung cấp nhan_vien_id trong request
                if (!$request->has('nhan_vien_id')) {
                    return response()->json([
                        'message' => 'Admin phải cung cấp nhan_vien_id',
                        'errors' => ['nhan_vien_id' => 'Bắt buộc']
                    ], 422);
                }
                $nhanVienId = $request->input('nhan_vien_id');
            } else {
                    return response()->json([
                    'message' => 'Chỉ nhân viên hoặc admin có thể tạo phiếu khám',
                    'errors' => ['auth' => 'Quyền hạn không đủ']
                ], 403);
            }

            // Thêm nhan_vien_id vào validated data
            $validated['nhan_vien_id'] = $nhanVienId;

            // Tạo phiếu khám
            $phieuKham = PhieuKham::create($validated);

            return response()->json([
                'message' => 'Thêm phiếu khám thành công',
                'data' => $phieuKham,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi thêm phiếu khám',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy danh sách tất cả phiếu khám
     */
    public function index()
    {
        try {
            $phieuKhams = PhieuKham::with(['lichHen', 'nhanVien'])
                ->paginate(15);

            return response()->json([
                'message' => 'Lấy danh sách phiếu khám thành công',
                'data' => $phieuKhams->items(),
                'pagination' => [
                    'total' => $phieuKhams->total(),
                    'per_page' => $phieuKhams->perPage(),
                    'current_page' => $phieuKhams->currentPage(),
                    'last_page' => $phieuKhams->lastPage(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi lấy danh sách phiếu khám',
                'error' => $e->getMessage(),
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
     * Lấy chi tiết phiếu khám bệnh
     */
    public function show($id)
    {
        try {
            $phieuKham = PhieuKham::with([
                'lichHen.thuCung',
                'lichHen.khachHang',
                'nhanVien'
            ])->find($id);

            if (!$phieuKham) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy phiếu khám với ID này'
                ], 404);
            }

            $thuCung = $phieuKham->lichHen?->thuCung;
            $khachHang = $phieuKham->lichHen?->khachHang;
            $lichHen = $phieuKham->lichHen;

            if (!$thuCung || !$khachHang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin thú cưng hoặc chủ nuôi'
                ], 404);
            }

            // Lấy lịch sử khám của cùng thú cưng
            $lichSuKham = PhieuKham::whereHas('lichHen', fn($q) => $q->where('thu_cung_id', $thuCung->id))
                ->with('nhanVien')
                ->orderByDesc('created_at')
                ->get();

            // Lịch sử cân nặng
            $lichSuCanNang = $lichSuKham->filter(fn($pk) => $pk->can_nang !== null)
                ->map(fn($pk) => [
                    'date' => $pk->created_at->format('m/Y'),
                    'value' => (float) $pk->can_nang
                ])->values();

            return response()->json([
                'success' => true,
                'message' => 'Lấy chi tiết phiếu khám thành công',
                'data' => [
                    'id' => $phieuKham->id,
                    'created_at' => $phieuKham->created_at->format('d/m/Y H:i'),
                    'pet' => [
                        'id' => $thuCung->id,
                        'image' => $thuCung->anh ?? null,
                        'name' => $thuCung->ten ?? 'Chưa đặt tên',
                        'species' => $thuCung->loai_thu_cung ?? 'Thú cưng',
                        'breed' => $thuCung->giong ?? '',
                        'age' => $thuCung->tuoi ?? null,
                        'gender' => $thuCung->gioi_tinh === 'male' ? 'Đực' : ($thuCung->gioi_tinh === 'female' ? 'Cái' : 'Không xác định'),
                        'color' => $thuCung->mau_trai ?? '',
                        'note' => $thuCung->ghi_chu ?? ''
                    ],
                    'owner' => [
                        'id' => $khachHang->id,
                        'full_name' => $khachHang->ho_ten ?? $khachHang->ten ?? 'Chưa có tên',
                        'phone' => $khachHang->so_dien_thoai ?? '',
                        'address' => $khachHang->dia_chi ?? '',
                        'email' => $khachHang->email ?? '',
                        'type' => $khachHang->la_thanh_vien ? 'member' : 'khach_vang_lai'
                    ],
                    'doctor' => [
                        'id' => $phieuKham->nhanVien?->id,
                        'name' => $phieuKham->nhanVien?->full_name ?? 'Chưa xác định',
                        'specialization' => $phieuKham->nhanVien?->chuyen_mon ?? ''
                    ],
                    'examination' => [
                        'reason' => $phieuKham->ly_do_den_kham ?? '',
                        'symptoms' => $phieuKham->trieu_chung ?? '',
                        'diagnosis' => $phieuKham->chan_doan ?? '',
                        'notes' => $phieuKham->ghi_chu ?? ''
                    ],
                    'vital_signs' => [
                        'temperature' => $phieuKham->nhiet_do ?? null,
                        'weight' => $phieuKham->can_nang ?? null,
                        'heart_rate' => $phieuKham->nhip_tim ?? null,
                        'respiratory_rate' => $phieuKham->nhip_tho ?? null
                    ],
                    'referral_type' => $phieuKham->loai_chi_dinh ?? '',
                    'appointment' => [
                        'id' => $lichHen?->id,
                        'date' => $lichHen?->ngay_hen ? \Carbon\Carbon::parse($lichHen->ngay_hen)->format('d/m/Y') : null,
                        'time' => $lichHen?->gio_hen ?? null,
                        'status' => $lichHen?->trang_thai ?? ''
                    ],
                    'histories' => $lichSuKham->map(fn($pk) => [
                        'id' => $pk->id,
                        'date' => $pk->created_at->format('d/m/Y'),
                        'time' => $pk->created_at->format('H:i'),
                        'reason' => $pk->ly_do_den_kham ?? 'Khám bệnh',
                        'doctor_name' => $pk->nhanVien?->full_name ?? 'Bác sĩ',
                        'symptoms' => $pk->trieu_chung ?? '',
                        'diagnosis' => $pk->chan_doan ?? '',
                        'treatment' => $pk->ghi_chu ?? '',
                        'weight' => $pk->can_nang ?? null
                    ]),
                    'weight_history' => $lichSuCanNang->toArray()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy chi tiết phiếu khám',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật phiếu khám
     */
    public function update(Request $request, PhieuKham $phieuKham)
    {
        try {
            // Validate dữ liệu cập nhật
            $validated = $request->validate([
                'nhiet_do' => 'nullable|numeric|between:30,45',
                'can_nang' => 'nullable|numeric|min:0',
                'nhip_tim' => 'nullable|integer|between:30,200',
                'nhip_tho' => 'nullable|integer|between:5,50',
                'ly_do_den_kham' => 'nullable|string|max:255',
                'trieu_chung' => 'nullable|string',
                'chan_doan' => 'nullable|string',
                'ghi_chu' => 'nullable|string',
                'loai_chi_dinh' => 'nullable|in:chi_dinh_can_lam_sang,don_thuoc,hen_tai_kham',
            ]);

            // Cập nhật phiếu khám
            $phieuKham->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật phiếu khám thành công',
                'data' => $phieuKham
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật phiếu khám',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa phiếu khám
     */
    public function destroy(PhieuKham $phieuKham)
    {
        try {
            $phieuKham->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa phiếu khám thành công'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa phiếu khám',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách phiếu khám theo thú cưng
     */
    public function getByPet($thuCungId)
    {
        try {
            $phieuKhams = PhieuKham::whereHas('lichHen', fn($q) => $q->where('thu_cung_id', $thuCungId))
                ->with(['lichHen.thuCung', 'lichHen.khachHang', 'nhanVien'])
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách phiếu khám của thú cưng thành công',
                'data' => $phieuKhams
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách phiếu khám',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách phiếu khám theo bác sĩ
     */
    public function getByDoctor($nhanVienId)
    {
        try {
            $phieuKhams = PhieuKham::where('nhan_vien_id', $nhanVienId)
                ->with(['lichHen.thuCung', 'lichHen.khachHang', 'nhanVien'])
                ->orderByDesc('created_at')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách phiếu khám của bác sĩ thành công',
                'data' => $phieuKhams->items(),
                'pagination' => [
                    'total' => $phieuKhams->total(),
                    'per_page' => $phieuKhams->perPage(),
                    'current_page' => $phieuKhams->currentPage(),
                    'last_page' => $phieuKhams->lastPage(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách phiếu khám',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thống kê phiếu khám hôm nay
     */
    public function getTodayExaminations()
    {
        try {
            $today = \Carbon\Carbon::today();

            $phieuKhams = PhieuKham::whereDate('created_at', $today)
                ->with(['lichHen.thuCung', 'lichHen.khachHang', 'nhanVien'])
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách phiếu khám hôm nay thành công',
                'data' => $phieuKhams,
                'total' => count($phieuKhams)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách phiếu khám',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
