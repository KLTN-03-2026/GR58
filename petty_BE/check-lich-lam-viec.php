<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== KIỂM TRA LỊCH LÀM VIỆC ===\n\n";

// 1. Kiểm tra tổng số lịch làm việc
$totalLichLamViec = \App\Models\LichLamViec::count();
echo "Tổng số lịch làm việc: $totalLichLamViec\n\n";

// 2. Lấy danh sách nhân viên
$nhanViens = \App\Models\NhanVien::all();
echo "=== DANH SÁCH NHÂN VIÊN ===\n";
foreach ($nhanViens as $nv) {
    $lichCount = \App\Models\LichLamViec::where('nhan_vien_id', $nv->id)->count();
    echo "ID: {$nv->id} | Tên: {$nv->full_name} | Số lịch: $lichCount\n";
}

echo "\n=== LỊCH LÀM VIỆC GẦN ĐÂY (10 bản ghi) ===\n";
$recentLich = \App\Models\LichLamViec::with('nhanVien')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

foreach ($recentLich as $lich) {
    echo "ID: {$lich->id} | Nhân viên: " . ($lich->nhanVien ? $lich->nhanVien->full_name : 'N/A') .
        " (ID: {$lich->nhan_vien_id}) | Ngày: {$lich->ngay_lam} | Ca: {$lich->thoi_gian_truc}\n";
}

echo "\n=== KIỂM TRA LỊCH LÀM VIỆC THEO NHÂN VIÊN ===\n";
if ($nhanViens->count() > 0) {
    $firstNhanVien = $nhanViens->first();
    echo "Kiểm tra lịch của nhân viên: {$firstNhanVien->full_name} (ID: {$firstNhanVien->id})\n";

    $lichCuaNhanVien = \App\Models\LichLamViec::where('nhan_vien_id', $firstNhanVien->id)
        ->orderBy('ngay_lam', 'desc')
        ->get();

    echo "Số lịch: {$lichCuaNhanVien->count()}\n";

    foreach ($lichCuaNhanVien as $lich) {
        echo "  - Ngày: {$lich->ngay_lam} | Ca: {$lich->thoi_gian_truc} | Ghi chú: " . ($lich->ghi_chu ?? 'N/A') . "\n";
    }
}

echo "\n=== KIỂM TRA CẤU TRÚC BẢNG ===\n";
try {
    $columns = \DB::select("DESCRIBE lich_lam_viecs");
    echo "Các cột trong bảng lich_lam_viecs:\n";
    foreach ($columns as $col) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }
} catch (\Exception $e) {
    echo "Lỗi khi kiểm tra cấu trúc bảng: " . $e->getMessage() . "\n";
}
