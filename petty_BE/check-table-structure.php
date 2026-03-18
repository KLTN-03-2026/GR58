<?php

/**
 * Script kiểm tra các cột trong bảng phan_quyens
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n=== KIỂM TRA CẤU TRÚC BẢNG PHAN_QUYENS ===\n\n";

try {
    // Lấy tất cả columns của bảng phan_quyens
    $columns = DB::select('SHOW COLUMNS FROM phan_quyens');

    echo "Các cột trong bảng phan_quyens:\n";
    echo str_repeat("-", 80) . "\n";

    $permissionColumns = [];
    foreach ($columns as $column) {
        echo sprintf("%-40s %-15s\n", $column->Field, $column->Type);

        // Lọc các cột quyền (có chứa _xem, _tao, _sua, _xoa)
        if (preg_match('/_(xem|tao|sua|xoa|doi|xuat|xac|thanh|khoa|mo)/', $column->Field)) {
            $permissionColumns[] = $column->Field;
        }
    }

    echo "\n" . str_repeat("-", 80) . "\n";
    echo "Tổng số cột: " . count($columns) . "\n";
    echo "Số cột quyền: " . count($permissionColumns) . "\n\n";

    echo "Danh sách các cột quyền:\n";
    echo str_repeat("-", 80) . "\n";
    foreach ($permissionColumns as $col) {
        echo "  - {$col}\n";
    }

    echo "\n";
} catch (\Exception $e) {
    echo "\n❌ LỖI: " . $e->getMessage() . "\n\n";
    exit(1);
}
