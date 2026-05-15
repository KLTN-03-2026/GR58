<?php

namespace App\Http\Controllers;

use App\Models\ThanhToan;
use App\Models\LichHen;
use App\Models\NhanVien;
use App\Models\Admin;
use App\Services\SepayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SepayController extends Controller
{
    public function __construct(
        protected SepayService $sepayService
    ) {}

    public function createPayment(Request $request): JsonResponse
    {
        if (!$this->sepayService->isConfigured()) {
            return response()->json([
                'status' => false,
                'message' => 'Chức năng thanh toán online chưa được cấu hình.',
            ], 503);
        }

        $request->validate([
            'lich_hen_id' => 'required|exists:lich_hens,id',
            'ghi_chu' => 'nullable|string',
        ]);

        $user = $request->user();
        $lichHen = LichHen::with(['dichVus', 'khachHang'])->findOrFail($request->lich_hen_id);

        if ($lichHen->thanh_toan_id) {
            return response()->json([
                'status' => false,
                'message' => 'Lịch hẹn này đã được thanh toán.',
            ], 422);
        }

        $existing = ThanhToan::where('lich_hen_id', $lichHen->id)
            ->where('hinh_thuc_thanh_toan', 'chuyen_khoan')
            ->where('trang_thai', 'cho_thanh_toan')
            ->first();

        if ($existing) {
            return response()->json([
                'status' => true,
                'message' => 'Giao dịch đang chờ thanh toán.',
                'data' => [
                    'thanh_toan' => $existing,
                    'payment_info' => $this->sepayService->getPaymentInfo($existing),
                ],
            ]);
        }

        $tongTienGoc = $lichHen->dichVus->sum(fn($dv) => $dv->pivot->thanh_tien ?? $dv->gia_tien);

        if ($tongTienGoc == 0 && $lichHen->dich_vu_id) {
            $tongTienGoc = $lichHen->dichVu?->gia_tien ?? 0;
        }

        if ($tongTienGoc <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Không có dịch vụ để thanh toán.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $thanhToan = ThanhToan::create([
                'ma_thanh_toan' => 'TT' . now()->format('ymdHis') . rand(10, 99),
                'lich_hen_id' => $lichHen->id,
                'khach_hang_id' => $lichHen->khach_hang_id,
                'tong_tien_goc' => $tongTienGoc,
                'so_tien_giam' => 0,
                'tong_tien_sau_giam' => $tongTienGoc,
                'hinh_thuc_thanh_toan' => 'chuyen_khoan',
                'tien_mat' => 0,
                'tien_online' => $tongTienGoc,
                'trang_thai' => 'cho_thanh_toan',
                'nhan_vien_id' => $user instanceof NhanVien ? $user->id : null,
                'admin_id' => $user instanceof Admin ? $user->id : null,
                'het_han_luc' => now()->addMinutes($this->sepayService->getExpiryMinutes()),
                'ghi_chu' => $request->ghi_chu,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Tạo giao dịch chuyển khoản thành công. Vui lòng thanh toán trong ' . $this->sepayService->getExpiryMinutes() . ' phút.',
                'data' => [
                    'thanh_toan' => $thanhToan,
                    'payment_info' => $this->sepayService->getPaymentInfo($thanhToan),
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Lỗi tạo giao dịch: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function webhook(Request $request): JsonResponse
    {
        $authHeader = $request->header('Authorization') ?? '';
        $apiKey = str_replace(['Bearer ', 'Apikey ', 'Bearer: '], '', $authHeader);

        if (empty($apiKey)) {
            $apiKey = $request->header('x-api-key')
                ?? $request->query('api_key')
                ?? $request->input('api_key')
                ?? '';
        }

        Log::info('SePay webhook received', [
            'ip' => $request->ip(),
            'headers' => $request->headers->all(),
            'body' => $request->all(),
        ]);

        if (!$this->sepayService->verifyWebhook($apiKey)) {
            Log::warning('SePay webhook: invalid API key', [
                'received_key' => substr($apiKey, 0, 10) . '...',
                'ip' => $request->ip(),
            ]);
            return response()->json(['success' => false], 401);
        }

        $parsed = $this->sepayService->parseTransaction($request->all());

        if (!$parsed) {
            return response()->json(['success' => true]);
        }

        if ($parsed['transfer_type'] !== 'in') {
            return response()->json(['success' => true]);
        }

        $thanhToan = $this->sepayService->matchTransaction($parsed);

        if (!$thanhToan) {
            Log::info('SePay webhook: no matching transaction', ['content' => $parsed['content']]);
            return response()->json(['success' => true]);
        }

        if ($parsed['amount'] < $thanhToan->tong_tien_sau_giam) {
            Log::warning('SePay webhook: insufficient amount', [
                'expected' => $thanhToan->tong_tien_sau_giam,
                'received' => $parsed['amount'],
                'ma_thanh_toan' => $thanhToan->ma_thanh_toan,
            ]);
            return response()->json(['success' => true]);
        }

        $thanhToan->update([
            'trang_thai' => 'da_thanh_toan',
            'ma_giao_dich_online' => $parsed['transaction_id'],
            'sepay_transaction_id' => $parsed['transaction_id'],
            'ngay_thanh_toan' => now(),
        ]);

        if ($thanhToan->lich_hen_id) {
            LichHen::where('id', $thanhToan->lich_hen_id)
                ->whereNull('thanh_toan_id')
                ->update([
                    'thanh_toan_id' => $thanhToan->id,
                    'da_thanh_toan' => true,
                ]);
        }

        Log::info('SePay webhook: payment confirmed', [
            'ma_thanh_toan' => $thanhToan->ma_thanh_toan,
            'amount' => $parsed['amount'],
        ]);

        return response()->json(['success' => true]);
    }

    public function checkStatus(int $id): JsonResponse
    {
        $thanhToan = ThanhToan::findOrFail($id);

        $data = [
            'trang_thai' => $thanhToan->trang_thai,
            'ngay_thanh_toan' => $thanhToan->ngay_thanh_toan,
        ];

        if ($thanhToan->isPending() && $thanhToan->het_han_luc) {
            $remaining = now()->diffInSeconds($thanhToan->het_han_luc, false);
            $data['thoi_gian_con_lai'] = max(0, $remaining);
        }

        return response()->json(['status' => true, 'data' => $data]);
    }

    public function confirmManual(Request $request, int $id): JsonResponse
    {
        $thanhToan = ThanhToan::findOrFail($id);

        if (!$thanhToan->isPending()) {
            return response()->json([
                'status' => false,
                'message' => 'Giao dịch không ở trạng thái chờ thanh toán.',
            ], 422);
        }

        $user = $request->user();

        $thanhToan->update([
            'trang_thai' => 'da_thanh_toan',
            'ngay_thanh_toan' => now(),
            'nhan_vien_id' => $user instanceof NhanVien ? $user->id : $thanhToan->nhan_vien_id,
            'admin_id' => $user instanceof Admin ? $user->id : $thanhToan->admin_id,
        ]);

        if ($thanhToan->lich_hen_id) {
            LichHen::where('id', $thanhToan->lich_hen_id)
                ->whereNull('thanh_toan_id')
                ->update([
                    'thanh_toan_id' => $thanhToan->id,
                    'da_thanh_toan' => true,
                ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Xác nhận thanh toán thành công.',
            'data' => $thanhToan->fresh(),
        ]);
    }
}
