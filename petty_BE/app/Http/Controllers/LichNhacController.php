<?php

namespace App\Http\Controllers;

use App\Models\LichNhac;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LichNhacController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = LichNhac::with('hoSoBenhAn');

            // Filter by status
            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            // Filter by category
            if ($request->has('phan_loai')) {
                $query->where('phan_loai', $request->phan_loai);
            }

            // Filter by date range
            if ($request->has('tu_ngay')) {
                $query->whereDate('ngay_nhac', '>=', $request->tu_ngay);
            }
            if ($request->has('den_ngay')) {
                $query->whereDate('ngay_nhac', '<=', $request->den_ngay);
            }

            // Filter by ho_so_benh_an_id
            if ($request->has('ho_so_benh_an_id')) {
                $query->where('ho_so_benh_an_id', $request->ho_so_benh_an_id);
            }

            // Get upcoming reminders (sắp tới)
            if ($request->has('sap_toi')) {
                $days = $request->input('sap_toi', 7);
                $query->sapToi($days);
            }

            // Sort by date
            $query->orderBy('ngay_nhac', $request->input('sort', 'asc'));

            $lichNhacs = $query->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách lịch nhắc thành công',
                'data' => $lichNhacs,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách lịch nhắc: ' . $e->getMessage(),
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
                'ngay_nhac' => 'required|date',
                'phan_loai' => 'required|string|max:100',
                'noi_dung' => 'required|string',
                'ghi_chu' => 'nullable|string',
                'trang_thai' => 'nullable|string|in:chua_gui,da_gui,hoan_thanh,huy',
                'ho_so_benh_an_id' => 'required|exists:ho_so_benh_ans,id',
            ], [
                'ngay_nhac.required' => 'Ngày nhắc là bắt buộc',
                'ngay_nhac.date' => 'Ngày nhắc không hợp lệ',
                'phan_loai.required' => 'Phân loại là bắt buộc',
                'noi_dung.required' => 'Nội dung là bắt buộc',
                'ho_so_benh_an_id.required' => 'Hồ sơ bệnh án là bắt buộc',
                'ho_so_benh_an_id.exists' => 'Hồ sơ bệnh án không tồn tại',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $lichNhac = LichNhac::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Tạo lịch nhắc thành công',
                'data' => $lichNhac->load('hoSoBenhAn'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo lịch nhắc: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(LichNhac $lichNhac)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Lấy thông tin lịch nhắc thành công',
                'data' => $lichNhac->load('hoSoBenhAn'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy thông tin lịch nhắc: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LichNhac $lichNhac)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ngay_nhac' => 'nullable|date',
                'phan_loai' => 'nullable|string|max:100',
                'noi_dung' => 'nullable|string',
                'ghi_chu' => 'nullable|string',
                'trang_thai' => 'nullable|string|in:chua_gui,da_gui,hoan_thanh,huy',
                'ho_so_benh_an_id' => 'nullable|exists:ho_so_benh_ans,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $lichNhac->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật lịch nhắc thành công',
                'data' => $lichNhac->load('hoSoBenhAn'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật lịch nhắc: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LichNhac $lichNhac)
    {
        try {
            $lichNhac->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa lịch nhắc thành công',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa lịch nhắc: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update status of reminder
     */
    public function updateStatus(Request $request, LichNhac $lichNhac)
    {
        try {
            $validator = Validator::make($request->all(), [
                'trang_thai' => 'required|string|in:chua_gui,da_gui,hoan_thanh,huy',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trạng thái không hợp lệ',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $lichNhac->update(['trang_thai' => $request->trang_thai]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công',
                'data' => $lichNhac,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get reminders by medical record
     */
    public function getByMedicalRecord($hoSoBenhAnId)
    {
        try {
            $lichNhacs = LichNhac::where('ho_so_benh_an_id', $hoSoBenhAnId)
                ->with('hoSoBenhAn')
                ->orderBy('ngay_nhac', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách lịch nhắc theo hồ sơ bệnh án thành công',
                'data' => $lichNhacs,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách lịch nhắc: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get categories
     */
    public function getCategories()
    {
        return response()->json([
            'success' => true,
            'data' => LichNhac::PHAN_LOAI,
        ], 200);
    }

    /**
     * Get statuses
     */
    public function getStatuses()
    {
        return response()->json([
            'success' => true,
            'data' => LichNhac::TRANG_THAI,
        ], 200);
    }
}
