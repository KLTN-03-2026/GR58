<?php
$content = file_get_contents('app/Http/Controllers/LichHenController.php');

// Remove transformData and transformLichHenModel methods
$content = preg_replace('/private function transformLichHenModel.*?private function transformData.*?return \$data;\n    \}/s', '', $content);

// Replace $payload = $this->transformData($lichHen); with $payload = new \App\Http\Resources\LichHenResource($lichHen);
$content = str_replace('$payload = $this->transformData($lichHen);', '$payload = new \App\Http\Resources\LichHenResource($lichHen);', $content);
$content = str_replace('$payload = $this->transformData($lichHen->fresh()->load([\'thuCung\', \'dichVu\', \'nhanVien\', \'yTaCheckin\', \'khachHang\']));', '$payload = new \App\Http\Resources\LichHenResource($lichHen->fresh()->load([\'thuCung\', \'dichVu\', \'nhanVien\', \'yTaCheckin\', \'khachHang\']));', $content);
$content = str_replace('$payload = $this->transformData($lichHen->load([\'thuCung\', \'dichVu\', \'nhanVien\', \'yTaCheckin\', \'thanhToan\', \'khachHang\']));', '$payload = new \App\Http\Resources\LichHenResource($lichHen->load([\'thuCung\', \'dichVu\', \'nhanVien\', \'yTaCheckin\', \'thanhToan\', \'khachHang\']));', $content);

// For collections/paginators
$content = str_replace('$payload = $this->transformData($data);', '$payload = \App\Http\Resources\LichHenResource::collection($data);', $content);

file_put_contents('app/Http/Controllers/LichHenController.php', $content);
