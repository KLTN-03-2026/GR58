<?php
/**
 * Script test authentication
 * Chạy: php test-auth.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Admin;
use Laravel\Sanctum\PersonalAccessToken;

echo "=== KIỂM TRA AUTHENTICATION ===\n\n";

// 1. Kiểm tra Admin
echo "1. Danh sách Admin:\n";
$admins = Admin::with('phanQuyen')->get();
foreach ($admins as $admin) {
    echo "   - {$admin->ho_ten} (Email: {$admin->email})\n";
    echo "     ID: {$admin->id}\n";
    echo "     Vai trò: " . ($admin->phanQuyen ? $admin->phanQuyen->ten_vai_tro : 'CHƯA CÓ') . "\n";
    echo "     Vai trò ID: {$admin->phan_quyen_id}\n";

    // Kiểm tra token
    $tokens = PersonalAccessToken::where('tokenable_type', 'App\\Models\\Admin')
        ->where('tokenable_id', $admin->id)
        ->get();
    echo "     Số token: " . $tokens->count() . "\n";

    if ($tokens->count() > 0) {
        foreach ($tokens as $token) {
            $expired = $token->expires_at && $token->expires_at < now();
            echo "       + Token: " . substr($token->token, 0, 10) . "... ";
            echo "(" . ($expired ? '❌ HẾT HẠN' : '✅ CÒN HẠN') . ")\n";
            if ($token->last_used_at) {
                echo "         Dùng lần cuối: {$token->last_used_at}\n";
            }
        }
    }
    echo "\n";
}

// 2. Test middleware StaffOnly
echo "\n2. Test logic middleware StaffOnly:\n";
$testAdmin = Admin::first();
if ($testAdmin) {
    echo "   Admin test: {$testAdmin->ho_ten}\n";
    echo "   instanceof Admin: " . ($testAdmin instanceof \App\Models\Admin ? '✅ TRUE' : '❌ FALSE') . "\n";
    echo "   instanceof NhanVien: " . ($testAdmin instanceof \App\Models\NhanVien ? '❌ TRUE' : '✅ FALSE') . "\n";
    echo "   instanceof KhachHang: " . ($testAdmin instanceof \App\Models\KhachHang ? '❌ TRUE' : '✅ FALSE') . "\n";
}

echo "\n3. Kiểm tra Sanctum config:\n";
echo "   Stateful domains: " . implode(', ', config('sanctum.stateful')) . "\n";
echo "   Token expiration: " . (config('sanctum.expiration') ?? 'null (không hết hạn)') . "\n";

echo "\n✅ Kiểm tra hoàn tất!\n";
echo "\n💡 Nếu vẫn lỗi:\n";
echo "   1. Đăng xuất hoàn toàn\n";
echo "   2. Xóa localStorage/cookie\n";
echo "   3. Đăng nhập lại\n";
echo "   4. Kiểm tra token được gửi trong header: Authorization: Bearer <token>\n";
