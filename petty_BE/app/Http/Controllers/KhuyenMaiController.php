<?php

namespace App\Http\Controllers;

use App\Models\KhuyenMai;
use App\Http\Requests\KhuyenMaiRequest;
use App\Http\Resources\KhuyenMaiResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KhuyenMaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = KhuyenMai::with('dichVus:id,ten,ma_dich_vu');

            // Lọc theo trạng thái
            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            // Lọc theo loại khuyến mãi
            if ($request->has('loai_khuyen_mai')) {
                $query->where('loai_khuyen_mai', $request->loai_khuyen_mai);
            }

            // Lọc theo loại khách hàng
            if ($request->has('loai_khach_hang')) {
                $query->where('loai_khach_hang', $request->loai_khach_hang);
            }

            // Tìm kiếm theo tên hoặc mã code
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('ten_khuyen_mai', 'like', "%{$search}%")
                        ->orWhere('ma_code', 'like', "%{$search}%");
                });
            }

            // Lọc khuyến mãi đang hoạt động (còn hiệu lực)
            if ($request->has('active') && $request->active == true) {
                $query->active();
            }

            // Sắp xếp
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Phân trang
            $perPage = $request->get('per_page', 10);
            $khuyenMais = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách khuyến mãi thành công',
                'data' => [
                    'data' => KhuyenMaiResource::collection($khuyenMais->items()),
                    'current_page' => $khuyenMais->currentPage(),
                    'last_page' => $khuyenMais->lastPage(),
                    'per_page' => $khuyenMais->perPage(),
                    'total' => $khuyenMais->total(),
                    'from' => $khuyenMais->firstItem(),
                    'to' => $khuyenMais->lastItem(),
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in KhuyenMaiController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách khuyến mãi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KhuyenMaiRequest $request)
    {
        DB::beginTransaction();
        try {
            // Tạo khuyến mãi
            $khuyenMai = KhuyenMai::create([
                'ten_khuyen_mai' => $request->ten_khuyen_mai,
                'mo_ta' => $request->mo_ta,
                'loai_khuyen_mai' => $request->loai_khuyen_mai,
                'ma_code' => $request->loai_khuyen_mai === 'ma_giam_gia' ? $request->ma_code : null,
                'gia_tri_don_toi_thieu' => $request->gia_tri_don_toi_thieu,
                'loai_khach_hang' => $request->loai_khach_hang,
                'hinh_thuc_giam' => $request->hinh_thuc_giam,
                'giam_toi_da' => $request->hinh_thuc_giam === 'phan_tram' ? $request->giam_toi_da : null,
                'gia_tri_giam' => $request->gia_tri_giam,
                'tu_ngay' => $request->tu_ngay,
                'den_ngay' => $request->den_ngay,
                'tong_so_luong' => $request->tong_so_luong,
                'so_luong_da_dung' => 0,
                'gioi_han_moi_khach' => $request->gioi_han_moi_khach,
                'trang_thai' => $request->trang_thai ?? KhuyenMai::TRANG_THAI_DANG_CHAY,
            ]);

            // Gắn dịch vụ nếu có
            if ($request->has('dich_vu_ids') && is_array($request->dich_vu_ids)) {
                $khuyenMai->dichVus()->attach($request->dich_vu_ids);
            }

            DB::commit();

            // Load lại quan hệ
            $khuyenMai->load('dichVus:id,ten,ma_dich_vu,gia_tien');

            return response()->json([
                'success' => true,
                'message' => 'Tạo khuyến mãi thành công',
                'data' => new KhuyenMaiResource($khuyenMai),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in KhuyenMaiController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo khuyến mãi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $khuyenMai = KhuyenMai::with('dichVus:id,ten,ma_dich_vu,gia_tien')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Lấy thông tin khuyến mãi thành công',
                'data' => new KhuyenMaiResource($khuyenMai),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in KhuyenMaiController@show: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khuyến mãi',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KhuyenMaiRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $khuyenMai = KhuyenMai::findOrFail($id);

            // Cập nhật thông tin
            $khuyenMai->update([
                'ten_khuyen_mai' => $request->ten_khuyen_mai,
                'mo_ta' => $request->mo_ta,
                'loai_khuyen_mai' => $request->loai_khuyen_mai,
                'ma_code' => $request->loai_khuyen_mai === 'ma_giam_gia' ? $request->ma_code : null,
                'gia_tri_don_toi_thieu' => $request->gia_tri_don_toi_thieu,
                'loai_khach_hang' => $request->loai_khach_hang,
                'hinh_thuc_giam' => $request->hinh_thuc_giam,
                'giam_toi_da' => $request->hinh_thuc_giam === 'phan_tram' ? $request->giam_toi_da : null,
                'gia_tri_giam' => $request->gia_tri_giam,
                'tu_ngay' => $request->tu_ngay,
                'den_ngay' => $request->den_ngay,
                'tong_so_luong' => $request->tong_so_luong,
                'gioi_han_moi_khach' => $request->gioi_han_moi_khach,
                'trang_thai' => $request->trang_thai ?? $khuyenMai->trang_thai,
            ]);

            // Cập nhật dịch vụ
            if ($request->has('dich_vu_ids')) {
                if (is_array($request->dich_vu_ids)) {
                    $khuyenMai->dichVus()->sync($request->dich_vu_ids);
                } else {
                    $khuyenMai->dichVus()->detach();
                }
            }

            DB::commit();

            // Load lại quan hệ
            $khuyenMai->load('dichVus:id,ten,ma_dich_vu,gia_tien');

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật khuyến mãi thành công',
                'data' => new KhuyenMaiResource($khuyenMai),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in KhuyenMaiController@update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật khuyến mãi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $khuyenMai = KhuyenMai::findOrFail($id);

            // Xóa quan hệ với dịch vụ
            $khuyenMai->dichVus()->detach();

            // Xóa khuyến mãi
            $khuyenMai->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xóa khuyến mãi thành công',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in KhuyenMaiController@destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa khuyến mãi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Đổi trạng thái khuyến mãi
     */
    public function changeStatus($id)
    {
        DB::beginTransaction();
        try {
            $khuyenMai = KhuyenMai::findOrFail($id);

            // Đổi trạng thái
            if ($khuyenMai->trang_thai === KhuyenMai::TRANG_THAI_DANG_CHAY) {
                $khuyenMai->trang_thai = KhuyenMai::TRANG_THAI_DA_KET_THUC;
                $message = 'Đã chuyển khuyến mãi sang trạng thái "Đã kết thúc"';
            } else {
                $khuyenMai->trang_thai = KhuyenMai::TRANG_THAI_DANG_CHAY;
                $message = 'Đã chuyển khuyến mãi sang trạng thái "Đang chạy"';
            }

            $khuyenMai->save();

            DB::commit();

            // Load lại quan hệ
            $khuyenMai->load('dichVus:id,ten,ma_dich_vu,gia_tien');

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => new KhuyenMaiResource($khuyenMai),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in KhuyenMaiController@changeStatus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đổi trạng thái khuyến mãi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Kiểm tra mã code có hợp lệ không
     */
    public function checkCode(Request $request)
    {
        try {
            $request->validate([
                'ma_code' => 'required|string',
                'gia_tri_don' => 'required|numeric|min:0',
            ]);

            $khuyenMai = KhuyenMai::where('ma_code', $request->ma_code)
                ->where('trang_thai', KhuyenMai::TRANG_THAI_DANG_CHAY)
                ->first();

            if (!$khuyenMai) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã khuyến mãi không tồn tại hoặc đã hết hạn',
                ], 404);
            }

            // Kiểm tra các điều kiện
            if (!$khuyenMai->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã khuyến mãi không còn hiệu lực',
                ], 400);
            }

            // Kiểm tra giá trị đơn tối thiểu
            if ($khuyenMai->gia_tri_don_toi_thieu && $request->gia_tri_don < $khuyenMai->gia_tri_don_toi_thieu) {
                return response()->json([
                    'success' => false,
                    'message' => "Đơn hàng phải có giá trị tối thiểu " . number_format($khuyenMai->gia_tri_don_toi_thieu) . " VNĐ",
                ], 400);
            }

            // Tính giá trị giảm
            $giaTriGiam = $khuyenMai->calculateDiscount($request->gia_tri_don);

            return response()->json([
                'success' => true,
                'message' => 'Mã khuyến mãi hợp lệ',
                'data' => [
                    'khuyen_mai' => $khuyenMai,
                    'gia_tri_giam' => $giaTriGiam,
                    'gia_tri_sau_giam' => $request->gia_tri_don - $giaTriGiam,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in KhuyenMaiController@checkCode: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kiểm tra mã khuyến mãi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
