<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== TEST getMySchedule ENDPOINT ===\n\n";

// Giả lập request từ NhanVien ID=1
$nhanVien = \App\Models\NhanVien::find(1);
if (!$nhanVien) {
    echo "Không tìm thấy nhân viên ID=1\n";
    exit;
}

echo "Nhân viên: {$nhanVien->full_name}\n";
echo "Email: {$nhanVien->email}\n\n";

// Tạo token cho nhân viên
$token = $nhanVien->createToken('test-token');

// Tạo request giả lập
$request = \Illuminate\Http\Request::create(
    '/api/lich-lam-viec/cua-toi',
    'GET',
    [
        'start_date' => '2025-12-01',
        'end_date' => '2025-12-31'
    ]
);

// Đặt token vào header
$request->headers->set('Authorization', 'Bearer ' . $token->plainTextToken);

// Giả lập authentication
$request->setUserResolver(function () use ($nhanVien) {
    return $nhanVien;
});

// Gọi controller
$controller = new \App\Http\Controllers\LichLamViecController();
$response = $controller->getMySchedule($request);

$data = json_decode($response->getContent(), true);

echo "=== KẾT QUẢ ===\n";
echo "Status: " . ($data['status'] ? 'TRUE' : 'FALSE') . "\n";
echo "Nhân viên: " . ($data['data']['nhan_vien']['full_name'] ?? 'N/A') . "\n";
echo "Khoảng thời gian: {$data['data']['period']['start_date']} đến {$data['data']['period']['end_date']}\n\n";

echo "Thống kê:\n";
echo "  - Số ngày làm việc: {$data['data']['statistics']['total_work_days']}\n";
echo "  - Số lịch hẹn: {$data['data']['statistics']['total_appointments']}\n\n";

echo "Chi tiết lịch (schedule):\n";
if (empty($data['data']['schedule'])) {
    echo "  KHÔNG CÓ LỊCH NÀO\n";
} else {
    foreach ($data['data']['schedule'] as $day) {
        echo "\n  Ngày: {$day['date']}\n";

        echo "    Lịch làm việc:\n";
        if (empty($day['lich_lam_viec'])) {
            echo "      - Không có ca làm việc\n";
        } else {
            foreach ($day['lich_lam_viec'] as $llv) {
                echo "      - Ca: {$llv['thoi_gian_truc']}\n";
            }
        }

        echo "    Lịch hẹn:\n";
        if (empty($day['lich_hen'])) {
            echo "      - Không có lịch hẹn\n";
        } else {
            foreach ($day['lich_hen'] as $lh) {
                echo "      - {$lh['ngay_gio']} | Khách: {$lh['khach_hang']} | Dịch vụ: {$lh['dich_vu']}\n";
            }
        }
    }
}

echo "\n=== RAW DATA (JSON) ===\n";
echo json_encode($data['data']['schedule'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
