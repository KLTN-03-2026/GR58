<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MoMoService;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $momoService;

    // "Tiêm" MoMoService vào Controller để sử dụng
    public function __construct(MoMoService $momoService)
    {
        $this->momoService = $momoService;
    }

    /**
     * API 1: Frontend gọi API này để lấy link thanh toán
     */
    public function createMoMoPayment(Request $request)
    {
        // Log request để debug
        Log::info('MoMo Payment Request:', $request->all());

        // 1. Nhận dữ liệu từ Frontend gửi lên
        $baseOrderId = $request->input('order_id', time());
        // Thêm timestamp để đảm bảo order_id luôn unique
        $orderId = $baseOrderId . '_' . time();
        $amount = $request->input('amount', 50000);
        $orderInfo = "Thanh toán hóa đơn #" . $baseOrderId;

        // Validate amount
        if (!is_numeric($amount) || $amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Số tiền không hợp lệ',
            ], 400);
        }

        // 2. Gọi MoMoService để tạo request
        try {
            $response = $this->momoService->createPayment($orderId, $amount, $orderInfo);

            Log::info('MoMo API Response:', $response);

            // 3. Trả về kết quả cho Frontend
            if (isset($response['payUrl'])) {
                return response()->json([
                    'success' => true,
                    'payUrl' => $response['payUrl']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $response['message'] ?? 'Lỗi tạo thanh toán MoMo',
                'data' => $response
            ], 400);
        } catch (\Exception $e) {
            Log::error('MoMo Payment Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API 2: MoMo gọi ẩn về API này để báo "Khách đã trả tiền thành công" (IPN)
     */
    public function momoIPN(Request $request)
    {
        // Ghi log lại để chúng ta dễ debug xem MoMo gửi gì về
        Log::info('MoMo IPN Request: ', $request->all());

        $resultCode = $request->input('resultCode');
        $orderId = $request->input('orderId');
        $amount = $request->input('amount');
        $transId = $request->input('transId');
        $payType = $request->input('payType');

        // resultCode = 0 nghĩa là giao dịch thành công
        if ($resultCode == 0) {

            // Tách orderId để lấy mã hóa đơn gốc (HD000019_1775916799 -> HD000019)
            $baseOrderId = explode('_', $orderId)[0];

            // Tìm lịch hẹn theo invoice code (HD000019 -> id = 19)
            $lichHenId = (int) str_replace('HD', '', $baseOrderId);
            $lichHen = \App\Models\LichHen::find($lichHenId);

            if ($lichHen) {
                // Cập nhật trạng thái thanh toán
                $lichHen->update([
                    'da_thanh_toan' => true,
                    'phuong_thuc_thanh_toan' => 'momo',
                    'thoi_gian_thanh_toan' => now(),
                ]);

                Log::info('Updated payment status for LichHen ID: ' . $lichHenId);
            }

            return response()->json(['message' => 'Đã nhận thông báo thành công'], 204);
        }

        return response()->json(['message' => 'Giao dịch thất bại'], 400);
    }

    /**
     * API 3: Frontend gọi để cập nhật trạng thái sau khi thanh toán thành công
     */
    public function updatePaymentStatus(Request $request)
    {
        $orderId = $request->input('order_id');
        $resultCode = $request->input('result_code');

        if ($resultCode != 0) {
            return response()->json([
                'success' => false,
                'message' => 'Giao dịch không thành công'
            ], 400);
        }

        // Tách orderId để lấy mã hóa đơn gốc
        $baseOrderId = explode('_', $orderId)[0];

        // Tìm lịch hẹn theo invoice code
        $lichHenId = (int) str_replace('HD', '', $baseOrderId);
        $lichHen = \App\Models\LichHen::find($lichHenId);

        if (!$lichHen) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy lịch hẹn'
            ], 404);
        }

        // Cập nhật trạng thái thanh toán
        $lichHen->update([
            'da_thanh_toan' => true,
            'phuong_thuc_thanh_toan' => 'momo',
            'thoi_gian_thanh_toan' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thanh toán thành công',
            'data' => $lichHen
        ]);
    }
}