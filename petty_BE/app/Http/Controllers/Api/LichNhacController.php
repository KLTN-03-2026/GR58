<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLichNhacRequest;
use App\Http\Resources\LichNhacResource;
use App\Models\HoSoBenhAn;
use App\Models\LichNhac;
use App\Models\PhieuKham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LichNhacController extends Controller
{
    public function store(StoreLichNhacRequest $request, $phieuKhamId)
    {
        $phieuKham = PhieuKham::with('lichHen.thuCung')->findOrFail($phieuKhamId);

        // Check ownership
        if ($phieuKham->nhan_vien_id !== auth()->id()) {
            return response()->json([
                'message' => 'Bạn không có quyền tạo lịch nhắc cho phiếu khám này.'
            ], 403);
        }

        $lichNhac = DB::transaction(function () use ($phieuKham, $request) {
            $lichHen = $phieuKham->lichHen;
            $thuCungId = $lichHen->thu_cung_id;
            $khachHangId = $lichHen->khach_hang_id;

            // Resolve ho_so_benh_an_id via firstOrCreate with lock
            $hoSoBenhAn = HoSoBenhAn::lockForUpdate()
                ->firstOrCreate(
                    [
                        'thu_cung_id' => $thuCungId,
                        'khach_hang_id' => $khachHangId,
                    ],
                    [
                        'ma_benh_an' => "BA-{$thuCungId}-{$khachHangId}",
                        'noi_dung' => '',
                    ]
                );

            // Create LichNhac
            return LichNhac::create([
                'ho_so_benh_an_id' => $hoSoBenhAn->id,
                'ngay_nhac' => $request->ngay_nhac,
                'phan_loai' => $request->phan_loai,
                'noi_dung' => $request->noi_dung,
                'ghi_chu' => $request->ghi_chu,
                'trang_thai' => 'chua_gui',
            ]);
        });

        return new LichNhacResource($lichNhac);
    }

    public function index($phieuKhamId)
    {
        $phieuKham = PhieuKham::with('lichHen')->findOrFail($phieuKhamId);

        $lichHen = $phieuKham->lichHen;
        $thuCungId = $lichHen->thu_cung_id;
        $khachHangId = $lichHen->khach_hang_id;

        // Find ho_so_benh_an
        $hoSoBenhAn = HoSoBenhAn::where('thu_cung_id', $thuCungId)
            ->where('khach_hang_id', $khachHangId)
            ->first();

        if (!$hoSoBenhAn) {
            return response()->json([]);
        }

        $lichNhacs = LichNhac::where('ho_so_benh_an_id', $hoSoBenhAn->id)->get();

        return LichNhacResource::collection($lichNhacs);
    }

    public function destroy($lichNhacId)
    {
        $lichNhac = LichNhac::findOrFail($lichNhacId);

        // Only staff can delete
        if (!auth()->guard('nhan_vien')->check() && !auth()->guard('admin')->check()) {
            return response()->json([
                'message' => 'Bạn không có quyền xóa lịch nhắc.'
            ], 403);
        }

        $lichNhac->delete();

        return response()->json([
            'message' => 'Đã xóa lịch nhắc.'
        ]);
    }
}
