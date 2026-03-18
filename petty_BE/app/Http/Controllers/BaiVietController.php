<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use App\Http\Requests\StoreBaiVietRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class BaiVietController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $baiViets = BaiViet::with(['nhanVien', 'phanLoai'])->get();

            // Transform response to include phan_loai details
            $baiViets = $baiViets->map(function ($baiViet) {
                return [
                    'id' => $baiViet->id,
                    'ten_bai_viet' => $baiViet->ten_bai_viet,
                    'slug' => $baiViet->slug,
                    'noi_dung' => $baiViet->noi_dung,
                    'mo_ta' => $baiViet->mo_ta,
                    'anh_bai_viet' => $baiViet->anh_bai_viet,
                    'trang_thai' => $baiViet->trang_thai,
                    'nhan_vien_id' => $baiViet->nhan_vien_id,
                    'phan_loai_bai_viet_id' => $baiViet->phan_loai_bai_viet_id,
                    'nhan_vien' => $baiViet->nhanVien,
                    'phan_loai' => [
                        'id' => $baiViet->phanLoai?->id,
                        'ten_phan_loai' => $baiViet->phanLoai?->ten_phan_loai,
                        'slug' => $baiViet->phanLoai?->slug,
                        'mo_ta' => $baiViet->phanLoai?->mo_ta,
                    ],
                    'created_at' => $baiViet->created_at,
                    'updated_at' => $baiViet->updated_at,
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách bài viết thành công',
                'data' => $baiViets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi lấy danh sách bài viết',
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
    public function store(StoreBaiVietRequest $request)
    {
        try {
            $validated = $request->validated();

            // Generate slug from ten_bai_viet
            $validated['slug'] = Str::slug($validated['ten_bai_viet']);

            // Get authenticated user (nhan_vien_id from current user)
            $user = $request->user();
            if ($user) {
                $validated['nhan_vien_id'] = $user->id;
            }

            // Set default status if not provided
            if (!isset($validated['trang_thai'])) {
                $validated['trang_thai'] = 'published';
            }

            $baiViet = BaiViet::create($validated);
            $baiViet->load(['nhanVien', 'phanLoai']);

            $response = [
                'id' => $baiViet->id,
                'ten_bai_viet' => $baiViet->ten_bai_viet,
                'slug' => $baiViet->slug,
                'noi_dung' => $baiViet->noi_dung,
                'mo_ta' => $baiViet->mo_ta,
                'anh_bai_viet' => $baiViet->anh_bai_viet,
                'trang_thai' => $baiViet->trang_thai,
                'nhan_vien_id' => $baiViet->nhan_vien_id,
                'phan_loai_bai_viet_id' => $baiViet->phan_loai_bai_viet_id,
                'nhan_vien' => $baiViet->nhanVien,
                'phan_loai' => [
                    'id' => $baiViet->phanLoai?->id,
                    'ten_phan_loai' => $baiViet->phanLoai?->ten_phan_loai,
                    'slug' => $baiViet->phanLoai?->slug,
                    'mo_ta' => $baiViet->phanLoai?->mo_ta,
                ],
                'created_at' => $baiViet->created_at,
                'updated_at' => $baiViet->updated_at,
            ];

            return response()->json([
                'status' => true,
                'message' => 'Tạo bài viết thành công',
                'data' => $response
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi tạo bài viết',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(BaiViet $baiViet)
    {
        try {
            $baiViet->load(['nhanVien', 'phanLoai']);

            $response = [
                'id' => $baiViet->id,
                'ten_bai_viet' => $baiViet->ten_bai_viet,
                'slug' => $baiViet->slug,
                'noi_dung' => $baiViet->noi_dung,
                'mo_ta' => $baiViet->mo_ta,
                'anh_bai_viet' => $baiViet->anh_bai_viet,
                'trang_thai' => $baiViet->trang_thai,
                'nhan_vien_id' => $baiViet->nhan_vien_id,
                'phan_loai_bai_viet_id' => $baiViet->phan_loai_bai_viet_id,
                'nhan_vien' => $baiViet->nhanVien,
                'phan_loai' => [
                    'id' => $baiViet->phanLoai?->id,
                    'ten_phan_loai' => $baiViet->phanLoai?->ten_phan_loai,
                    'slug' => $baiViet->phanLoai?->slug,
                    'mo_ta' => $baiViet->phanLoai?->mo_ta,
                ],
                'created_at' => $baiViet->created_at,
                'updated_at' => $baiViet->updated_at,
            ];

            return response()->json([
                'status' => true,
                'message' => 'Lấy chi tiết bài viết thành công',
                'data' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi lấy chi tiết bài viết',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BaiViet $baiViet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BaiViet $baiViet)
    {
        try {
            $rules = [
                'ten_bai_viet' => 'sometimes|required|string|max:255',
                'noi_dung' => 'sometimes|required|string',
                'mo_ta' => 'nullable|string',
                'anh_bai_viet' => 'nullable|string',
                'trang_thai' => 'nullable|in:published,hidden',
                'phan_loai_bai_viet_id' => 'nullable|exists:phan_loai_bai_viets,id',
            ];

            $validated = $request->validate($rules);

            // Generate slug if ten_bai_viet is being updated
            if (isset($validated['ten_bai_viet'])) {
                $validated['slug'] = Str::slug($validated['ten_bai_viet']);
            }

            $baiViet->update($validated);
            $baiViet->load(['nhanVien', 'phanLoai']);

            $response = [
                'id' => $baiViet->id,
                'ten_bai_viet' => $baiViet->ten_bai_viet,
                'slug' => $baiViet->slug,
                'noi_dung' => $baiViet->noi_dung,
                'mo_ta' => $baiViet->mo_ta,
                'anh_bai_viet' => $baiViet->anh_bai_viet,
                'trang_thai' => $baiViet->trang_thai,
                'nhan_vien_id' => $baiViet->nhan_vien_id,
                'phan_loai_bai_viet_id' => $baiViet->phan_loai_bai_viet_id,
                'nhan_vien' => $baiViet->nhanVien,
                'phan_loai' => [
                    'id' => $baiViet->phanLoai?->id,
                    'ten_phan_loai' => $baiViet->phanLoai?->ten_phan_loai,
                    'slug' => $baiViet->phanLoai?->slug,
                    'mo_ta' => $baiViet->phanLoai?->mo_ta,
                ],
                'created_at' => $baiViet->created_at,
                'updated_at' => $baiViet->updated_at,
            ];

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật bài viết thành công',
                'data' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi cập nhật bài viết',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BaiViet $baiViet)
    {
        try {
            $baiViet->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa bài viết thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi xóa bài viết',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
