<?php

namespace App\Http\Controllers;

use App\Http\Requests\DanhMucHangHoaRequest;
use App\Models\DanhMucHangHoa;
use Illuminate\Http\JsonResponse;

class DanhMucHangHoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $items = DanhMucHangHoa::orderBy('created_at', 'desc')->get();
        return response()->json([
            'message' => 'Lấy danh sách danh mục hàng hóa thành công.',
            'data' => $items,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DanhMucHangHoaRequest $request): JsonResponse
    {
        $data = $request->validated();
        $item = DanhMucHangHoa::create($data);
        return response()->json([
            'message' => 'Tạo danh mục hàng hóa thành công.',
            'data' => $item,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(DanhMucHangHoa $danhMucHangHoa): JsonResponse
    {
        return response()->json([
            'message' => 'Lấy thông tin danh mục thành công.',
            'data' => $danhMucHangHoa,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DanhMucHangHoaRequest $request, DanhMucHangHoa $danhMucHangHoa): JsonResponse
    {
        $danhMucHangHoa->update($request->validated());
        return response()->json([
            'message' => 'Cập nhật danh mục hàng hóa thành công.',
            'data' => $danhMucHangHoa,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DanhMucHangHoa $danhMucHangHoa): JsonResponse
    {
        $danhMucHangHoa->delete();
        return response()->json([
            'message' => 'Xóa danh mục hàng hóa thành công.'
        ], 200);
    }
}
