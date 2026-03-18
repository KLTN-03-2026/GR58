<?php

namespace App\Http\Controllers;

use App\Models\PhanLoaiBaiViet;
use App\Http\Requests\StorePhanLoaiBaiVietRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PhanLoaiBaiVietController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $phanLoai = PhanLoaiBaiViet::all();
            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách phân loại bài viết thành công',
                'data' => $phanLoai
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi lấy danh sách phân loại bài viết',
                'error' => $e->getMessage()
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
     * Store a newly created resource in storage.
     */
    public function store(StorePhanLoaiBaiVietRequest $request)
    {
        try {
            $validated = $request->validated();

            // Generate slug from ten_phan_loai
            $validated['slug'] = Str::slug($validated['ten_phan_loai']);

            $phanLoai = PhanLoaiBaiViet::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Tạo phân loại bài viết thành công',
                'data' => $phanLoai
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi tạo phân loại bài viết',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PhanLoaiBaiViet $phanLoaiBaiViet)
    {
        try {
            return response()->json([
                'status' => true,
                'message' => 'Lấy chi tiết phân loại bài viết thành công',
                'data' => $phanLoaiBaiViet
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi lấy chi tiết phân loại bài viết',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PhanLoaiBaiViet $phanLoaiBaiViet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PhanLoaiBaiViet $phanLoaiBaiViet)
    {
        try {
            $rules = [
                'ten_phan_loai' => 'sometimes|required|string|max:255|unique:phan_loai_bai_viets,ten_phan_loai,' . $phanLoaiBaiViet->id,
                'mo_ta' => 'nullable|string',
            ];

            $validated = $request->validate($rules);

            // Generate slug if ten_phan_loai is being updated
            if (isset($validated['ten_phan_loai'])) {
                $validated['slug'] = Str::slug($validated['ten_phan_loai']);
            }

            $phanLoaiBaiViet->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật phân loại bài viết thành công',
                'data' => $phanLoaiBaiViet
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi cập nhật phân loại bài viết',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PhanLoaiBaiViet $phanLoaiBaiViet)
    {
        try {
            $phanLoaiBaiViet->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa phân loại bài viết thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi xóa phân loại bài viết',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
