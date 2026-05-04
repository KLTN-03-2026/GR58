<?php

namespace App\Http\Controllers;

use App\Models\YeuCauHoTro;
use Illuminate\Http\Request;

class YeuCauHoTroController extends Controller
{
    /**
     * Gửi yêu cầu hỗ trợ mới (public hoặc authenticated).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ho_ten'        => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'so_dien_thoai' => 'nullable|string|max:20',
            'chu_de'        => 'required|string|max:255',
            'noi_dung'      => 'required|string',
        ]);

        // Gán khach_hang_id nếu đã đăng nhập
        $user = $request->user();
        if ($user && $user instanceof \App\Models\KhachHang) {
            $validated['khach_hang_id'] = $user->id;
        }

        $yeuCau = YeuCauHoTro::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Yêu cầu hỗ trợ đã được gửi thành công. Chúng tôi sẽ liên hệ trong 24h.',
            'data'    => $yeuCau,
        ], 201);
    }

    /**
     * Danh sách yêu cầu hỗ trợ (staff only).
     */
    public function index(Request $request)
    {
        $query = YeuCauHoTro::with('khachHang:id,full_name,email')
            ->orderByDesc('created_at');

        if ($request->has('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $items = $query->paginate($request->integer('per_page', 20));

        return response()->json([
            'status' => true,
            'data'   => $items->items(),
            'meta'   => [
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
                'total'        => $items->total(),
            ],
        ]);
    }

    /**
     * Cập nhật trạng thái / phản hồi yêu cầu (staff only).
     */
    public function update(Request $request, YeuCauHoTro $yeuCauHoTro)
    {
        $validated = $request->validate([
            'trang_thai' => 'sometimes|in:chua_xu_ly,dang_xu_ly,da_xu_ly',
            'phan_hoi'   => 'nullable|string',
        ]);

        $yeuCauHoTro->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật thành công',
            'data'    => $yeuCauHoTro,
        ]);
    }
}
