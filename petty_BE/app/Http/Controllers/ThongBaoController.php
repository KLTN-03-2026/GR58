<?php

namespace App\Http\Controllers;

use App\Http\Resources\ThongBaoResource;
use App\Models\ThongBao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ThongBaoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $thongBaos = ThongBao::where('khach_hang_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'status' => true,
            'data' => ThongBaoResource::collection($thongBaos),
            'pagination' => [
                'current_page' => $thongBaos->currentPage(),
                'last_page' => $thongBaos->lastPage(),
                'per_page' => $thongBaos->perPage(),
                'total' => $thongBaos->total(),
            ],
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $user = $request->user();
        $count = ThongBao::where('khach_hang_id', $user->id)
            ->where('da_doc', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $thongBao = ThongBao::find($id);

        if (!$thongBao) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy thông báo.'], 404);
        }

        if ($thongBao->khach_hang_id !== $user->id) {
            return response()->json(['status' => false, 'message' => 'Không có quyền truy cập.'], 403);
        }

        $thongBao->update(['da_doc' => true]);

        return response()->json(['status' => true, 'message' => 'Đã đánh dấu đã đọc.']);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();
        ThongBao::where('khach_hang_id', $user->id)
            ->where('da_doc', false)
            ->update(['da_doc' => true]);

        return response()->json(['status' => true, 'message' => 'Đã đánh dấu tất cả đã đọc.']);
    }
}
