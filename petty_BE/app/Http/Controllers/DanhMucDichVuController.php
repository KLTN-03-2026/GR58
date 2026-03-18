<?php

namespace App\Http\Controllers;

use App\Models\DanhMucDichVu;
use App\Http\Requests\StoreDanhMucDichVuRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Throwable;

class DanhMucDichVuController extends Controller
{

    public function index()
    {
        $items = DanhMucDichVu::select('id', 'ten_nhom', 'mo_ta')->get();

        return response()->json([
            'status' => true,
            'data' => $items,
        ]);
    }


    public function store(StoreDanhMucDichVuRequest $request)
    {
        try {
            $data = $request->validated();

            $danhMuc = DanhMucDichVu::create([
                'ten_nhom' => $data['ten_nhom'],
                'mo_ta' => $data['mo_ta'] ?? null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Tạo danh mục dịch vụ thành công.',
                'data' => $danhMuc,
            ], 201);
        } catch (Throwable $e) {
            // Log the exception and return a generic error message
            report($e);
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi tạo danh mục. Vui lòng thử lại sau.',
            ], 500);
        }
    }

    public function show(DanhMucDichVu $danhMucDichVu)
    {
        return response()->json([
            'status' => true,
            'data' => $danhMucDichVu->only(['id', 'ten_nhom', 'mo_ta']),
        ]);
    }

    public function destroy(DanhMucDichVu $danhMucDichVu)
    {
        try {
            // If there are services linked to this category, prevent deletion
            if ($danhMucDichVu->dichVus()->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không thể xóa danh mục này vì còn dịch vụ liên kết. Vui lòng gỡ liên kết hoặc xóa các dịch vụ trước.'
                ], 400);
            }

            $danhMucDichVu->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa danh mục dịch vụ thành công.'
            ], 200);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi xóa danh mục. Vui lòng thử lại sau.'
            ], 500);
        }
    }
}
