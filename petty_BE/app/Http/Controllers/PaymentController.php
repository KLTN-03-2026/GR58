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
        // 1. Nhận dữ liệu từ Frontend gửi lên (Ví dụ: ID lịch hẹn, số tiền)
        // Dùng tạm time() làm mã đơn hàng ngẫu nhiên nếu bạn chưa truyền order_id
        $orderId = $request->input('order_id', time()); 
        $amount = $request->input('amount', 50000); // Mặc định test 50.000đ
        $orderInfo = "Thanh toán hóa đơn #" . $orderId;

        // 2. Gọi MoMoService để tạo request
        $response = $this->momoService->createPayment($orderId, $amount, $orderInfo);

        // 3. Trả về kết quả cho Frontend
        if (isset($response['payUrl'])) {
            return response()->json([
                'success' => true,
                'payUrl' => $response['payUrl'] // Đây là link để Vue chuyển hướng khách hàng tới
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Lỗi tạo thanh toán MoMo',
            'data' => $response
        ], 400);
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

        // resultCode = 0 nghĩa là giao dịch thành công
        if ($resultCode == 0) {
            
            // TẠI ĐÂY: Bạn sẽ viết code cập nhật Database
            // Ví dụ: Đổi trạng thái lịch hẹn thành 'da_thanh_toan'
            // LichHen::where('id', $orderId)->update(['trang_thai' => 'da_thanh_toan']);

            return response()->json(['message' => 'Đã nhận thông báo thành công'], 204);
        }

        return response()->json(['message' => 'Giao dịch thất bại'], 400);
    }
}