<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDonThuocRequest;
use App\Http\Resources\ChiTietPhieuKhamResource;
use App\Models\ChiTietPhieuKham;
use App\Models\PhieuKham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonThuocController extends Controller
{
    public function index($phieuKhamId)
    {
        $phieuKham = PhieuKham::findOrFail($phieuKhamId);

        $chiTietPhieuKhams = $phieuKham->chiTietPhieuKhams()->with('hangHoa')->get();

        return ChiTietPhieuKhamResource::collection($chiTietPhieuKhams);
    }

    public function store(StoreDonThuocRequest $request, $phieuKhamId)
    {
        $phieuKham = PhieuKham::with('lichHen')->findOrFail($phieuKhamId);

        // Check ownership: bác sĩ là người khám
        if ($phieuKham->nhan_vien_id !== auth()->id()) {
            return response()->json([
                'message' => 'Bạn không có quyền chỉnh sửa đơn thuốc của phiếu khám này.'
            ], 403);
        }

        // Check phiếu khám chưa thanh toán
        if ($phieuKham->lichHen && $phieuKham->lichHen->trang_thai === 'da_thanh_toan') {
            return response()->json([
                'message' => 'Không thể chỉnh sửa đơn thuốc của phiếu khám đã thanh toán.'
            ], 409);
        }

        DB::transaction(function () use ($phieuKham, $request) {
            // Delete existing items (replace logic)
            $phieuKham->chiTietPhieuKhams()->delete();

            // Create new items
            foreach ($request->items as $item) {
                $phieuKham->chiTietPhieuKhams()->create($item);
            }
        });

        $chiTietPhieuKhams = $phieuKham->chiTietPhieuKhams()->with('hangHoa')->get();

        return ChiTietPhieuKhamResource::collection($chiTietPhieuKhams);
    }

    public function destroy($phieuKhamId, $chiTietId)
    {
        $phieuKham = PhieuKham::with('lichHen')->findOrFail($phieuKhamId);

        // Check ownership
        if ($phieuKham->nhan_vien_id !== auth()->id()) {
            return response()->json([
                'message' => 'Bạn không có quyền xóa đơn thuốc của phiếu khám này.'
            ], 403);
        }

        // Check phiếu khám chưa thanh toán
        if ($phieuKham->lichHen && $phieuKham->lichHen->trang_thai === 'da_thanh_toan') {
            return response()->json([
                'message' => 'Không thể xóa đơn thuốc của phiếu khám đã thanh toán.'
            ], 409);
        }

        $chiTiet = ChiTietPhieuKham::where('phieu_kham_id', $phieuKhamId)
            ->where('id', $chiTietId)
            ->firstOrFail();

        $chiTiet->delete();

        return response()->json([
            'message' => 'Đã xóa thuốc khỏi đơn thuốc.'
        ]);
    }
}
