<?php
/**
 * Script test API Phân quyền
 * Chạy: php test-phan-quyen-api.php
 */

// Test lấy danh sách phân quyền
echo "=== TEST 1: Lấy danh sách phân quyền ===\n";
$ch = curl_init('http://localhost:8000/api/phan-quyen');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($httpCode == 401) {
    echo "⚠️  Cần đăng nhập để test API này\n";
} else {
    $data = json_decode($response, true);
    if (isset($data['data'])) {
        echo "✅ Số lượng vai trò: " . count($data['data']) . "\n";
        foreach ($data['data'] as $role) {
            echo "   - {$role['ten_vai_tro']} (ID: {$role['id']})\n";
        }
    } else {
        echo "❌ Không có dữ liệu\n";
        echo "Response: $response\n";
    }
}

echo "\n=== TEST 2: Lấy danh sách tất cả quyền ===\n";
$ch = curl_init('http://localhost:8000/api/phan-quyen/danh-sach/tat-ca-quyen');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($httpCode == 401) {
    echo "⚠️  Cần đăng nhập để test API này\n";
} else {
    $data = json_decode($response, true);
    if (isset($data['data'])) {
        echo "✅ Số nhóm quyền: " . count($data['data']) . "\n";
        foreach ($data['data'] as $group => $info) {
            echo "   - {$info['label']}: " . count($info['permissions']) . " quyền\n";
        }
    } else {
        echo "❌ Không có dữ liệu\n";
        echo "Response: $response\n";
    }
}

echo "\n=== KẾT LUẬN ===\n";
echo "Nếu thấy HTTP Code 401: Cần đăng nhập với token Bearer\n";
echo "Nếu API hoạt động: Frontend có thể gọi được\n";
