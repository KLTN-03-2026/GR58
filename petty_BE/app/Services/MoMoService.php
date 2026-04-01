<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MoMoService
{
    /**
     * Hàm tạo yêu cầu thanh toán gửi sang MoMo
     * * @param string $orderId Mã đơn hàng (ví dụ: mã lịch hẹn)
     * @param int $amount Số tiền cần thanh toán
     * @param string $orderInfo Thông tin đơn hàng (ví dụ: "Thanh toán lịch khám #123")
     * @return array Trả về Pay URL để chuyển hướng khách hàng
     */
    public function createPayment($orderId, $amount, $orderInfo)
    {
        $partnerCode = config('momo.partner_code');
        $accessKey = config('momo.access_key');
        $secretKey = config('momo.secret_key');
        $endpoint = config('momo.endpoint');

        // MoMo yêu cầu request id phải là duy nhất mỗi lần gọi API
        $requestId = time() . "_" . $orderId; 
        
        // Dữ liệu thô dùng để tạo chữ ký (phải nối chuỗi đúng thứ tự MoMo yêu cầu)
        $rawHash = "accessKey=" . $accessKey .
            "&amount=" . $amount .
            "&extraData=" . "" .
            "&ipnUrl=" . config('momo.notify_url') .
            "&orderId=" . $orderId .
            "&orderInfo=" . $orderInfo .
            "&partnerCode=" . $partnerCode .
            "&redirectUrl=" . config('momo.return_url') .
            "&requestId=" . $requestId .
            "&requestType=captureWallet";

        // Tạo chữ ký bảo mật HMAC SHA256
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        // Đóng gói dữ liệu gửi đi
        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => "Phòng Khám Thú Cưng",
            'storeId' => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => config('momo.return_url'),
            'ipnUrl' => config('momo.notify_url'),
            'lang' => 'vi',
            'extraData' => "",
            'requestType' => 'captureWallet',
            'signature' => $signature
        ];

        // Gửi request sang MoMo bằng Http Client của Laravel
        $response = Http::post($endpoint, $data);

        return $response->json(); // Trả về kết quả từ MoMo (chứa link thanh toán)
    }
}