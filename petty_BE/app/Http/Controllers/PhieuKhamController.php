<?php

namespace App\Http\Controllers;

use App\Models\PhieuKham;
use Illuminate\Http\Request;
use App\Http\Requests\StorePhieuKhamRequest;
use App\Http\Requests\UpdatePhieuKhamRequest;

class PhieuKhamController extends Controller
{
    /**
     * Thêm mới phiếu khám
     */
    public function store(StorePhieuKhamRequest $request)
    {
        try {
            // Validate dữ liệu
            $validated = $request->validated();

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
                'data' => new \App\Http\Resources\PhieuKhamResource($phieuKham),
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
    public function index(Request $request)
    {
        try {
            $query = PhieuKham::with([
                'lichHen.thuCung',
                'lichHen.khachHang',
                'nhanVien',
            ]);

            if ($request->filled('loai_chi_dinh')) {
                $query->where('loai_chi_dinh', $request->get('loai_chi_dinh'));
            }

            if ($request->filled('from_date')) {
                $query->where('created_at', '>=', \Carbon\Carbon::parse($request->get('from_date'))->startOfDay());
            }

            if ($request->filled('to_date')) {
                $query->where('created_at', '<=', \Carbon\Carbon::parse($request->get('to_date'))->endOfDay());
            }

            if ($request->filled('search')) {
                $keyword = $request->get('search');
                $query->where(function ($q) use ($keyword) {
                    $q->where('id', $keyword)
                        ->orWhereHas('lichHen.thuCung', function ($petQuery) use ($keyword) {
                            $petQuery->where('ten_thu_cung', 'like', "%{$keyword}%")
                                ->orWhere('ten', 'like', "%{$keyword}%");
                        });
                });
            }

            $perPage = (int) $request->get('per_page', 15);
            $phieuKhams = $query
                ->orderByDesc('created_at')
                ->paginate($perPage);

            return response()->json([
                'message' => 'Lấy danh sách phiếu khám thành công',
                'data' => \App\Http\Resources\PhieuKhamResource::collection($phieuKhams->items()),
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
    public function update(UpdatePhieuKhamRequest $request, PhieuKham $phieuKham)
    {
        try {
            // Validate dữ liệu cập nhật
            $validated = $request->validated();

            // Cập nhật phiếu khám
            if (array_key_exists('ket_qua_can_lam_sang', $validated) && !empty($validated['ket_qua_can_lam_sang'])) {
                $validated['thoi_gian_tra_ket_qua'] = $validated['thoi_gian_tra_ket_qua'] ?? now();
            }

            $phieuKham->update($validated);
            $phieuKham->load(['lichHen.thuCung', 'lichHen.khachHang', 'nhanVien']);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật phiếu khám thành công',
                'data' => new \App\Http\Resources\PhieuKhamResource($phieuKham)
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
                'data' => \App\Http\Resources\PhieuKhamResource::collection($phieuKhams->items()),
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
