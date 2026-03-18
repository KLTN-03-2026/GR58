<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== KIỂM TRA LỊCH HẸN ===\n\n";

// 1. Kiểm tra tổng số lịch hẹn
$totalLichHen = \App\Models\LichHen::count();
echo "Tổng số lịch hẹn: $totalLichHen\n\n";

// 2. Kiểm tra lịch hẹn có nhân viên
$lichHenCoNhanVien = \App\Models\LichHen::whereNotNull('nhan_vien_id')->count();
$lichHenKhongNhanVien = \App\Models\LichHen::whereNull('nhan_vien_id')->count();

echo "Lịch hẹn có nhân viên: $lichHenCoNhanVien\n";
echo "Lịch hẹn chưa có nhân viên: $lichHenKhongNhanVien\n\n";

// 3. Lấy danh sách lịch hẹn gần đây
echo "=== LỊCH HẸN GẦN ĐÂY (10 bản ghi) ===\n";
$recentLichHen = \App\Models\LichHen::with(['nhanVien', 'khachHang', 'dichVu'])
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

foreach ($recentLichHen as $lich) {
    echo "ID: {$lich->id} | Khách hàng: " . ($lich->khachHang ? $lich->khachHang->ho_ten : 'N/A') .
        " | Nhân viên: " . ($lich->nhanVien ? $lich->nhanVien->full_name : 'CHƯA PHÂN CÔNG') .
        " (ID: " . ($lich->nhan_vien_id ?? 'NULL') . ") | Ngày: {$lich->ngay_gio} | Trạng thái: {$lich->trang_thai}\n";
}

echo "\n=== KIỂM TRA CẤU TRÚC BẢNG ===\n";
try {
    $columns = \DB::select("DESCRIBE lich_hens");
    echo "Các cột trong bảng lich_hens:\n";
    foreach ($columns as $col) {
        echo "  - {$col->Field} ({$col->Type}) " . ($col->Null === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
    }
} catch (\Exception $e) {
    echo "Lỗi khi kiểm tra cấu trúc bảng: " . $e->getMessage() . "\n";
}

echo "\n=== KIỂM TRA LỊCH HẸN CỦA NHÂN VIÊN ID=1 ===\n";
$lichHenNV1 = \App\Models\LichHen::where('nhan_vien_id', 1)
    ->with(['khachHang', 'dichVu'])
    ->orderBy('ngay_gio', 'desc')
    ->get();

echo "Số lịch hẹn của nhân viên ID=1: {$lichHenNV1->count()}\n";
foreach ($lichHenNV1 as $lich) {
    echo "  - Ngày: {$lich->ngay_gio} | Khách: " . ($lich->khachHang ? $lich->khachHang->ho_ten : 'N/A') .
        " | Dịch vụ: " . ($lich->dichVu ? $lich->dichVu->ten : 'N/A') . " | Trạng thái: {$lich->trang_thai}\n";
}
