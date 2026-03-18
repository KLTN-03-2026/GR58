<?php

namespace App\Http\Controllers;

use App\Models\DichVu;
use App\Http\Requests\StoreDichVuRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DichVuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // By default return full records (all columns) with related category name.
        // If the client needs the compact id/name list for selection UIs, pass ?only_brief=1
        // Supports optional pagination via ?per_page=25
        $query = DichVu::with(['danhMuc:id,ten_nhom'])->orderBy('ten');

        if (request()->boolean('only_brief')) {
            $items = $query->get(['id', 'ten']);
            return response()->json(['status' => true, 'data' => $items]);
        }

        $perPage = (int) request()->query('per_page', 0);
        if ($perPage > 0) {
            $p = $query->paginate($perPage);
            // Map items to include ten_nhom at top level
            $data = array_map(function ($item) {
                $arr = $item->toArray();
                $arr['ten_nhom'] = $item->danhMuc ? $item->danhMuc->ten_nhom : null;
                return $arr;
            }, $p->items());

            return response()->json([
                'status' => true,
                'data' => $data,
                'meta' => [
                    'current_page' => $p->currentPage(),
                    'per_page' => $p->perPage(),
                    'total' => $p->total(),
                    'last_page' => $p->lastPage(),
                ],
            ]);
        }

        $items = $query->get();
        $items = $items->map(function ($item) {
            $arr = $item->toArray();
            $arr['ten_nhom'] = $item->danhMuc ? $item->danhMuc->ten_nhom : null;
            return $arr;
        });

        return response()->json(['status' => true, 'data' => $items]);
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
    public function store(Request $request)
    {
        // Note: route protected by EnsureAdmin middleware
        $data = $request->validate([
            'ten' => 'required|string|max:255',
            'gia_tien' => 'required|numeric|min:0',
            'thoi_gian_thuc_hien' => 'nullable|integer|min:0',
            'mo_ta' => 'nullable|string',
            'ma_dich_vu' => 'nullable|string|max:100|unique:dich_vus,ma_dich_vu',
            'huong_dan' => 'nullable|string',
            'anh_dich_vu' => 'nullable|string',
            'trang_thai' => 'required|in:kinh_doanh,ngung',
            'danh_muc_id' => 'nullable|exists:danh_muc_dich_vus,id',
        ]);

        $dichVu = DichVu::create($data);

        // include the category name if available
        $tenNhom = null;
        if ($dichVu->danh_muc_id) {
            $tenNhom = $dichVu->danhMuc ? $dichVu->danhMuc->ten_nhom : null;
        }

        return response()->json([
            'status' => true,
            'message' => 'Tạo dịch vụ thành công.',
            'data' => array_merge($dichVu->toArray(), ['ten_nhom' => $tenNhom]),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(DichVu $dichVu)
    {
        $dichVu->load('danhMuc:id,ten_nhom');
        $arr = $dichVu->toArray();
        $arr['ten_nhom'] = $dichVu->danhMuc ? $dichVu->danhMuc->ten_nhom : null;

        return response()->json([
            'status' => true,
            'data' => $arr,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DichVu $dichVu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DichVu $dichVu)
    {
        // Note: route should be protected by EnsureAdmin middleware
        $data = $request->validate([
            'ten' => 'required|string|max:255',
            'gia_tien' => 'required|numeric|min:0',
            'thoi_gian_thuc_hien' => 'nullable|integer|min:0',
            'mo_ta' => 'nullable|string',
            'ma_dich_vu' => 'nullable|string|max:100|unique:dich_vus,ma_dich_vu,' . $dichVu->id,
            'huong_dan' => 'nullable|string',
            'anh_dich_vu' => 'nullable|string',
            'trang_thai' => 'required|in:kinh_doanh,ngung',
            'danh_muc_id' => 'nullable|exists:danh_muc_dich_vus,id',
        ]);

        try {
            // If a file is uploaded in the request (field 'file'), store it and set anh_dich_vu
            if ($request->hasFile('file')) {
                $fileValidator = Validator::make($request->all(), [
                    'file' => 'file|image|mimes:jpg,jpeg,png,gif|max:5120',
                ]);
                if ($fileValidator->fails()) {
                    return response()->json(['status' => false, 'message' => 'Ảnh không hợp lệ', 'errors' => $fileValidator->errors()], 422);
                }

                $file = $request->file('file');
                if ($file && $file->isValid()) {
                    try {
                        $path = $file->store('dichvu/images', 'public');
                        $publicUrl = url(Storage::url($path));
                        $data['anh_dich_vu'] = $publicUrl;
                    } catch (\Throwable $ue) {
                        Log::error('Service image store failed', ['message' => $ue->getMessage()]);
                        return response()->json(['status' => false, 'message' => 'Lưu ảnh thất bại.'], 500);
                    }
                }
            }

            $dichVu->fill($data);
            $dichVu->save();

            $dichVu->load('danhMuc:id,ten_nhom');
            $arr = $dichVu->toArray();
            $arr['ten_nhom'] = $dichVu->danhMuc ? $dichVu->danhMuc->ten_nhom : null;

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật dịch vụ thành công.',
                'data' => $arr,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['status' => false, 'message' => 'Có lỗi khi cập nhật dịch vụ.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DichVu $dichVu)
    {
        try {
            // Prevent deletion if linked to appointments
            if ($dichVu->lichHens()->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không thể xóa dịch vụ vì còn lịch hẹn liên quan.'
                ], 400);
            }

            $dichVu->delete();

            return response()->json(['status' => true, 'message' => 'Xóa dịch vụ thành công.'], 200);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['status' => false, 'message' => 'Có lỗi khi xóa dịch vụ.'], 500);
        }
    }
}
