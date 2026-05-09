<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadDinhKemRequest;
use App\Http\Resources\DinhKemPhieuKhamResource;
use App\Models\DinhKemPhieuKham;
use App\Models\PhieuKham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DinhKemPhieuKhamController extends Controller
{
    /**
     * Lấy danh sách đính kèm của một phiếu khám.
     */
    public function index($phieuKhamId)
    {
        $phieuKham = PhieuKham::findOrFail($phieuKhamId);

        $danhSach = DinhKemPhieuKham::where('phieu_kham_id', $phieuKham->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy danh sách đính kèm thành công.',
            'data'    => DinhKemPhieuKhamResource::collection($danhSach),
        ]);
    }

    /**
     * Upload file đính kèm cho phiếu khám.
     */
    public function store(UploadDinhKemRequest $request, $phieuKhamId)
    {
        $phieuKham = PhieuKham::findOrFail($phieuKhamId);

        $file    = $request->file('file');
        $mime    = $file->getMimeType();

        // Chuẩn hóa extension từ MIME — không tin extension client gửi lên
        $extMap  = [
            'image/jpeg'      => 'jpg',
            'image/png'       => 'png',
            'application/pdf' => 'pdf',
        ];
        $ext      = $extMap[$mime] ?? $file->getClientOriginalExtension();
        $uuid     = Str::uuid()->toString();
        $fileName = "{$uuid}.{$ext}";
        $folder   = "phieu-kham/{$phieuKham->id}";

        $path = $file->storeAs($folder, $fileName, 'public');

        $dinhKem = DinhKemPhieuKham::create([
            'phieu_kham_id'   => $phieuKham->id,
            'ten_file'        => $file->getClientOriginalName(),
            'duong_dan'       => $path,
            'loai_mime'       => $mime,
            'kich_thuoc'      => $file->getSize(),
            'nguoi_upload_id' => auth()->id(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Upload file thành công.',
            'data'    => new DinhKemPhieuKhamResource($dinhKem),
        ], 201);
    }

    /**
     * Xóa file đính kèm (chỉ người upload hoặc Admin).
     */
    public function destroy($phieuKhamId, $dinhKemId)
    {
        $phieuKham = PhieuKham::findOrFail($phieuKhamId);
        $dinhKem   = DinhKemPhieuKham::where('phieu_kham_id', $phieuKham->id)
            ->findOrFail($dinhKemId);

        $user = auth()->user();

        // Kiểm tra quyền: chính người upload hoặc Admin
        if ((int) $dinhKem->nguoi_upload_id !== (int) $user->id && !$user->hasRole('admin')) {
            return response()->json([
                'status'  => false,
                'message' => 'Bạn không có quyền xóa file này.',
            ], 403);
        }

        DB::transaction(function () use ($dinhKem) {
            Storage::disk('public')->delete($dinhKem->duong_dan);
            $dinhKem->delete();
        });

        return response()->json([
            'status'  => true,
            'message' => 'Xóa file đính kèm thành công.',
        ], 200);
    }
}
