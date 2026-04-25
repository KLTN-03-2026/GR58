<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLichNghiRequest;
use App\Http\Resources\LichNghiResource;
use App\Models\LichNghi;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LichNghiController extends Controller
{
    public function store(StoreLichNghiRequest $request): JsonResponse
    {
        $user     = $request->user();
        $ngayNghi = Carbon::parse($request->ngay_nghi);

        // Đếm số ngày nghỉ trong cùng tháng (chưa bị từ chối)
        $countThisMonth = LichNghi::where('nhan_vien_id', $user->id)
            ->whereMonth('ngay_nghi', $ngayNghi->month)
            ->whereYear('ngay_nghi', $ngayNghi->year)
            ->whereIn('trang_thai', [LichNghi::CHO_DUYET, LichNghi::DA_DUYET])
            ->count();

        // Quota 4 ngày nghỉ phép/tháng; vượt → không lương
        $loaiNghi = $countThisMonth < 4 ? LichNghi::CO_LUONG : LichNghi::KHONG_LUONG;

        $lichNghi = LichNghi::create([
            'nhan_vien_id'  => $user->id,
            'ngay_bat_dau'  => $ngayNghi->toDateString(),   // required by table schema
            'ngay_ket_thuc' => $ngayNghi->toDateString(),   // required by table schema
            'ngay_nghi'     => $ngayNghi->toDateString(),
            'ly_do'         => $request->ly_do,
            'trang_thai'    => LichNghi::CHO_DUYET,
            'loai_nghi'     => $loaiNghi,
        ]);

        return response()->json([
            'status'  => true,
            'message' => $loaiNghi === LichNghi::CO_LUONG
                ? 'Đăng ký nghỉ phép thành công (có lương), đang chờ duyệt'
                : 'Đăng ký nghỉ thành công (không lương — đã vượt 4 ngày phép tháng này), đang chờ duyệt',
            'data'    => new LichNghiResource($lichNghi->load('nhanVien')),
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $list = LichNghi::with('nhanVien')
            ->where('nhan_vien_id', $user->id)
            ->orderBy('ngay_nghi')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => LichNghiResource::collection($list),
        ]);
    }

    /**
     * Trả về quota nghỉ phép của nhân viên trong tháng được chỉ định.
     * GET /lich-nghi/quota?thang=5&nam=2026
     */
    public function quota(Request $request): JsonResponse
    {
        $user  = $request->user();
        $thang = (int) ($request->thang ?? now()->month);
        $nam   = (int) ($request->nam   ?? now()->year);

        $used = LichNghi::where('nhan_vien_id', $user->id)
            ->whereMonth('ngay_nghi', $thang)
            ->whereYear('ngay_nghi', $nam)
            ->whereIn('trang_thai', [LichNghi::CHO_DUYET, LichNghi::DA_DUYET])
            ->count();

        return response()->json([
            'status' => true,
            'data'   => [
                'thang'        => $thang,
                'nam'          => $nam,
                'tong_quota'   => 4,
                'da_su_dung'   => $used,
                'con_lai'      => max(0, 4 - $used),
            ],
        ]);
    }

    public function destroy(Request $request, LichNghi $lichNghi): JsonResponse
    {
        $user = $request->user();

        if ($lichNghi->nhan_vien_id !== $user->id) {
            return response()->json(['status' => false, 'message' => 'Không có quyền thực hiện'], 403);
        }

        if ($lichNghi->trang_thai === LichNghi::DA_DUYET) {
            throw ValidationException::withMessages([
                'trang_thai' => ['Không thể hủy lịch nghỉ đã được duyệt'],
            ]);
        }

        $lichNghi->delete();

        return response()->json(['status' => true, 'message' => 'Đã hủy lịch nghỉ']);
    }

    public function indexAll(Request $request): JsonResponse
    {
        $query = LichNghi::with('nhanVien');

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        if ($request->filled('nhan_vien_id')) {
            $query->where('nhan_vien_id', $request->nhan_vien_id);
        }

        if ($request->filled('thang')) {
            $query->whereMonth('ngay_nghi', $request->thang);
        }

        if ($request->filled('nam')) {
            $query->whereYear('ngay_nghi', $request->nam);
        }

        $list = $query->orderBy('ngay_nghi')->get();

        return response()->json([
            'status' => true,
            'data'   => LichNghiResource::collection($list),
        ]);
    }

    public function duyet(Request $request, LichNghi $lichNghi): JsonResponse
    {
        if ($lichNghi->trang_thai !== LichNghi::CHO_DUYET) {
            throw ValidationException::withMessages([
                'trang_thai' => ['Lịch nghỉ đã được xử lý'],
            ]);
        }

        $lichNghi->update([
            'trang_thai' => LichNghi::DA_DUYET,
            'admin_id'   => $request->user()->id,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Đã duyệt lịch nghỉ',
            'data'    => new LichNghiResource($lichNghi->load('nhanVien')),
        ]);
    }

    public function tuChoi(Request $request, LichNghi $lichNghi): JsonResponse
    {
        $request->validate([
            'ly_do' => ['required', 'string', 'max:500'],
        ]);

        if ($lichNghi->trang_thai !== LichNghi::CHO_DUYET) {
            throw ValidationException::withMessages([
                'trang_thai' => ['Lịch nghỉ đã được xử lý'],
            ]);
        }

        $lichNghi->update([
            'trang_thai'    => LichNghi::TU_CHOI,
            'ly_do_tu_choi' => $request->ly_do,
            'admin_id'      => $request->user()->id,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Đã từ chối lịch nghỉ',
            'data'    => new LichNghiResource($lichNghi->load('nhanVien')),
        ]);
    }

    public function storeAdmin(Request $request): JsonResponse
    {
        $request->validate([
            'nhan_vien_id' => ['required', 'exists:nhan_viens,id'],
            'ngay_nghi'    => ['required', 'date'],
            'ly_do'        => ['nullable', 'string', 'max:500'],
        ]);

        $ngayNghi = Carbon::parse($request->ngay_nghi)->toDateString();

        $exists = LichNghi::where('nhan_vien_id', $request->nhan_vien_id)
            ->where('ngay_nghi', $ngayNghi)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'ngay_nghi' => ['Ngày nghỉ này đã được đăng ký cho nhân viên này'],
            ]);
        }

        $lichNghi = LichNghi::create([
            'nhan_vien_id' => $request->nhan_vien_id,
            'ngay_bat_dau' => $ngayNghi,
            'ngay_ket_thuc' => $ngayNghi,
            'ngay_nghi'    => $ngayNghi,
            'ly_do'        => $request->ly_do,
            'trang_thai'   => LichNghi::DA_DUYET,
            'admin_id'     => $request->user()->id,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Đã tạo lịch nghỉ cho nhân viên',
            'data'    => new LichNghiResource($lichNghi->load('nhanVien')),
        ], 201);
    }

    /**
     * Gợi ý nhân viên cùng vai trò có thể thay thế ca trực.
     * GET /admin/lich-nghi/{lichNghi}/suggest
     *
     * Loại trừ:
     *  1. Nhân viên đang nghỉ (đã duyệt/chờ duyệt) vào ngày đó
     *  2. Nhân viên đã có ca trực ngày đó
     *  3. Nhân viên có ca TỐI ngày hôm trước (cần nghỉ ngơi)
     */
    public function suggestThayThe(Request $request, LichNghi $lichNghi): JsonResponse
    {
        $ngayNghi     = Carbon::parse($lichNghi->ngay_nghi);
        $ngayHomTruoc = $ngayNghi->copy()->subDay();

        // Vai trò của người xin nghỉ
        $vaiTro = $lichNghi->nhanVien?->chuc_vu ?? null;

        // 1. Đang nghỉ ngày đó (chờ duyệt hoặc đã duyệt)
        $dangNghi = LichNghi::whereIn('trang_thai', [LichNghi::CHO_DUYET, LichNghi::DA_DUYET])
            ->whereDate('ngay_nghi', $ngayNghi)
            ->pluck('nhan_vien_id');

        // 2. Đã có ca trực ngày đó
        $coTrucHomNay = \App\Models\LichLamViec::whereDate('ngay_lam', $ngayNghi)
            ->pluck('nhan_vien_id');

        // 3. Có ca TỐI ngày hôm trước
        $caToi = \App\Models\LichLamViec::whereDate('ngay_lam', $ngayHomTruoc)
            ->where('thoi_gian_truc', 'ca_toi')
            ->pluck('nhan_vien_id');

        $loaiTru = $dangNghi->merge($coTrucHomNay)->merge($caToi)
            ->push($lichNghi->nhan_vien_id)
            ->unique()->values();

        $query = \App\Models\NhanVien::whereNotIn('id', $loaiTru);

        if ($vaiTro) {
            $query->where('vai_tro', $vaiTro);
        }

        $suggestions = $query->get()->map(fn($nv) => [
            'id'      => $nv->id,
            'ho_ten'  => $nv->full_name,
            'chuc_vu' => $nv->chuc_danh,
            'avatar'  => $nv->anh_dai_dien,
            'ly_do'   => 'Rảnh ngày ' . $ngayNghi->format('d/m/Y') . ' — không có ca tối ngày ' . $ngayHomTruoc->format('d/m/Y'),
        ]);

        return response()->json([
            'status'    => true,
            'ngay_nghi' => $ngayNghi->format('d/m/Y'),
            'data'      => $suggestions,
        ]);
    }
}
