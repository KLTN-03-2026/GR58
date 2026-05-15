<?php

namespace App\Http\Controllers;

use App\Models\DichVu;
use App\Helpers\ImageHelper;
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
        $query = DichVu::with(['danhMuc:id,ten_nhom'])->orderBy('ten');

        if (request()->boolean('only_brief')) {
            $items = $query->get(['id', 'ten']);
            return response()->json(['status' => true, 'data' => $items]);
        }

        $perPage = (int) request()->query('per_page', 0);
        if ($perPage > 0) {
            $p    = $query->paginate($perPage);
            $data = array_map(function ($item) {
                $arr             = $item->toArray();
                $arr['ten_nhom'] = $item->danhMuc ? $item->danhMuc->ten_nhom : null;
                $arr['anh_dich_vu'] = $this->resolveImageUrl($arr['anh_dich_vu'] ?? null);
                return $arr;
            }, $p->items());

            return response()->json([
                'status' => true,
                'data'   => $data,
                'meta'   => [
                    'current_page' => $p->currentPage(),
                    'per_page'     => $p->perPage(),
                    'total'        => $p->total(),
                    'last_page'    => $p->lastPage(),
                ],
            ]);
        }

        $items = $query->get()->map(function ($item) {
            $arr             = $item->toArray();
            $arr['ten_nhom'] = $item->danhMuc ? $item->danhMuc->ten_nhom : null;
            $arr['anh_dich_vu'] = $this->resolveImageUrl($arr['anh_dich_vu'] ?? null);
            return $arr;
        });

        return response()->json(['status' => true, 'data' => $items]);
    }

    /**
     * Store a newly created resource in storage.
     * Hỗ trợ cả multipart/form-data (có ảnh) và JSON (không có ảnh)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'ten'                 => 'required|string|max:255',
            'gia_tien'            => 'required|numeric|min:0',
            'thoi_gian_thuc_hien' => 'nullable|integer|min:0',
            'mo_ta'               => 'nullable|string',
            'ma_dich_vu'          => 'nullable|string|max:100|unique:dich_vus,ma_dich_vu',
            'huong_dan'           => 'nullable|string',
            'anh_dich_vu'         => 'nullable|string',
            'trang_thai'          => 'required|in:kinh_doanh,ngung',
            'danh_muc_id'         => 'nullable|exists:danh_muc_dich_vus,id',
        ]);

        // ✅ Xử lý upload ảnh (nếu có)
        if ($request->hasFile('anh_dich_vu_file')) {
            $fileValidator = Validator::make($request->all(), [
                'anh_dich_vu_file' => 'file|image|mimes:jpg,jpeg,png,gif,webp|max:10240',
            ]);

            if ($fileValidator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Ảnh không hợp lệ',
                    'errors'  => $fileValidator->errors(),
                ], 422);
            }

            $file = $request->file('anh_dich_vu_file');
            if ($file && $file->isValid()) {
                try {
                    $path                 = $file->store('dichvu/images', 'public');
                    $data['anh_dich_vu']  = $path;
                } catch (\Throwable $e) {
                    Log::error('Service image store failed (create)', ['message' => $e->getMessage()]);
                    return response()->json(['status' => false, 'message' => 'Lưu ảnh thất bại.'], 500);
                }
            }
        }

        $dichVu  = DichVu::create($data);
        $tenNhom = $dichVu->danh_muc_id && $dichVu->danhMuc
            ? $dichVu->danhMuc->ten_nhom
            : null;

        $arr                 = $dichVu->toArray();
        $arr['ten_nhom']     = $tenNhom;
        $arr['anh_dich_vu']  = $this->resolveImageUrl($arr['anh_dich_vu'] ?? null);

        return response()->json([
            'status'  => true,
            'message' => 'Tạo dịch vụ thành công.',
            'data'    => $arr,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(DichVu $dichVu)
    {
        $dichVu->load('danhMuc:id,ten_nhom');
        $arr                 = $dichVu->toArray();
        $arr['ten_nhom']     = $dichVu->danhMuc ? $dichVu->danhMuc->ten_nhom : null;
        $arr['anh_dich_vu']  = $this->resolveImageUrl($arr['anh_dich_vu'] ?? null);

        return response()->json(['status' => true, 'data' => $arr]);
    }

    /**
     * Update the specified resource in storage.
     * Hỗ trợ cả multipart/form-data (có ảnh) và JSON
     */
    public function update(Request $request, DichVu $dichVu)
    {
        $data = $request->validate([
            'ten'                 => 'required|string|max:255',
            'gia_tien'            => 'required|numeric|min:0',
            'thoi_gian_thuc_hien' => 'nullable|integer|min:0',
            'mo_ta'               => 'nullable|string',
            'ma_dich_vu'          => 'nullable|string|max:100|unique:dich_vus,ma_dich_vu,' . $dichVu->id,
            'huong_dan'           => 'nullable|string',
            'anh_dich_vu'         => 'nullable|string',
            'trang_thai'          => 'required|in:kinh_doanh,ngung',
            'danh_muc_id'         => 'nullable|exists:danh_muc_dich_vus,id',
        ]);

        try {
            // ✅ Hỗ trợ cả field 'file' (cũ) và 'anh_dich_vu_file' (mới)
            $uploadField = $request->hasFile('anh_dich_vu_file')
                ? 'anh_dich_vu_file'
                : ($request->hasFile('file') ? 'file' : null);

            if ($uploadField) {
                $fileValidator = Validator::make($request->all(), [
                    $uploadField => 'file|image|mimes:jpg,jpeg,png,gif,webp|max:10240',
                ]);

                if ($fileValidator->fails()) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Ảnh không hợp lệ',
                        'errors'  => $fileValidator->errors(),
                    ], 422);
                }

                $file = $request->file($uploadField);
                if ($file && $file->isValid()) {
                    // Xóa ảnh cũ nếu là file local
                    if ($dichVu->anh_dich_vu) {
                        $oldPath = $this->extractStoragePath($dichVu->anh_dich_vu);
                        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }

                    try {
                        $path                = $file->store('dichvu/images', 'public');
                        $data['anh_dich_vu'] = $path;
                    } catch (\Throwable $ue) {
                        Log::error('Service image store failed (update)', ['message' => $ue->getMessage()]);
                        return response()->json(['status' => false, 'message' => 'Lưu ảnh thất bại.'], 500);
                    }
                }
            }

            $dichVu->fill($data)->save();
            $dichVu->load('danhMuc:id,ten_nhom');

            $arr                 = $dichVu->toArray();
            $arr['ten_nhom']     = $dichVu->danhMuc ? $dichVu->danhMuc->ten_nhom : null;
            $arr['anh_dich_vu']  = $this->resolveImageUrl($arr['anh_dich_vu'] ?? null);

            return response()->json([
                'status'  => true,
                'message' => 'Cập nhật dịch vụ thành công.',
                'data'    => $arr,
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
            if ($dichVu->lichHens()->exists()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Không thể xóa dịch vụ vì còn lịch hẹn liên quan.',
                ], 400);
            }

            // Xóa ảnh khi xóa dịch vụ
            if ($dichVu->anh_dich_vu) {
                $oldPath = $this->extractStoragePath($dichVu->anh_dich_vu);
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $dichVu->delete();

            return response()->json(['status' => true, 'message' => 'Xóa dịch vụ thành công.'], 200);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['status' => false, 'message' => 'Có lỗi khi xóa dịch vụ.'], 500);
        }
    }

    // ─── Helpers ──────────────────────────────────────────────────────

    /**
     * Đảm bảo trả về URL đầy đủ cho ảnh
     */
    private function resolveImageUrl(?string $path): ?string
    {
        return ImageHelper::resolveUrl($path);
    }

    /**
     * Lấy relative path từ full URL để xóa file
     */
    private function extractStoragePath(?string $url): ?string
    {
        if (!$url) return null;
        // URL dạng: http://example.com/storage/dichvu/images/abc.jpg
        // → storage path: dichvu/images/abc.jpg
        if (preg_match('/\/storage\/(.+)$/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }
}