<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChiTietHoSoBenhAnResource;
use App\Models\HoSoBenhAn;
use App\Models\KhachHang;
use App\Models\LichHen;
use App\Models\LichNhac;
use App\Models\PhieuKham;
use App\Models\ThuCung;
use Illuminate\Http\Request;

class ChiTietHoSoBenhAnController extends Controller
{
    public function timeline($thuCungId)
    {
        $thuCung = ThuCung::findOrFail($thuCungId);

        // Get khach_hang_id from request
        $khachHangId = request('khach_hang_id');
        if (!$khachHangId) {
            return response()->json([
                'message' => 'Thiếu tham số khach_hang_id'
            ], 400);
        }

        $khachHang = KhachHang::findOrFail($khachHangId);

        // Find or get ho_so_benh_an
        $hoSoBenhAn = HoSoBenhAn::where('thu_cung_id', $thuCungId)
            ->where('khach_hang_id', $khachHangId)
            ->first();

        // Get all phieu_khams for this pet
        $phieuKhams = PhieuKham::whereHas('lichHen', function ($q) use ($thuCungId) {
                $q->where('thu_cung_id', $thuCungId);
            })
            ->with([
                'nhanVien',
                'chiTietPhieuKhams.hangHoa',
                'dinhKems',
                'lichHen'
            ])
            ->orderByDesc('created_at')
            ->get();

        // Attach lich_nhacs to each phieu_kham if ho_so_benh_an exists
        if ($hoSoBenhAn) {
            $lichNhacs = LichNhac::where('ho_so_benh_an_id', $hoSoBenhAn->id)->get();

            foreach ($phieuKhams as $phieuKham) {
                // Attach lich_nhacs created around the same time as phieu_kham
                $phieuKham->lichNhacs = $lichNhacs->filter(function ($lichNhac) use ($phieuKham) {
                    return $lichNhac->created_at->diffInDays($phieuKham->created_at) <= 1;
                })->values();
            }

            // Get pending lich_nhacs
            $lichNhacsDangCho = $lichNhacs->where('trang_thai', 'chua_gui')
                ->where('ngay_nhac', '>=', now())
                ->values();
        } else {
            $lichNhacsDangCho = collect([]);
        }

        $data = [
            'thu_cung' => $thuCung,
            'khach_hang' => $khachHang,
            'ho_so_benh_an' => $hoSoBenhAn,
            'phieu_khams' => $phieuKhams,
            'lich_nhacs_dang_cho' => $lichNhacsDangCho,
        ];

        return new ChiTietHoSoBenhAnResource($data);
    }
}
