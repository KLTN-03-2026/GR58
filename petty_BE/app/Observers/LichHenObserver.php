<?php

namespace App\Observers;

use App\Models\LichHen;
use App\Services\ThongBaoService;

class LichHenObserver
{
    public function updated(LichHen $lichHen): void
    {
        $service = app(ThongBaoService::class);
        $khachHangId = $lichHen->khach_hang_id;

        if (!$khachHangId) return;

        if ($lichHen->wasChanged('trang_thai')) {
            $trangThai = $lichHen->trang_thai;
            $ngayGio = $lichHen->ngay_gio?->format('d/m/Y H:i') ?? '';

            if ($trangThai === 'confirmed') {
                $service->create(
                    $khachHangId,
                    'lich_hen',
                    'Lịch hẹn đã được xác nhận',
                    "Lịch hẹn của bạn vào {$ngayGio} đã được xác nhận.",
                    'LichHen',
                    $lichHen->id
                );
            } elseif ($trangThai === 'cancelled') {
                $service->create(
                    $khachHangId,
                    'lich_hen',
                    'Lịch hẹn đã bị hủy',
                    "Lịch hẹn của bạn vào {$ngayGio} đã bị hủy.",
                    'LichHen',
                    $lichHen->id
                );
            }
        }

        if ($lichHen->wasChanged('ngay_gio') && !$lichHen->wasChanged('trang_thai')) {
            $ngayGioMoi = $lichHen->ngay_gio?->format('d/m/Y H:i') ?? '';
            $service->create(
                $khachHangId,
                'lich_hen',
                'Lịch hẹn đã được đổi lịch',
                "Lịch hẹn của bạn đã được đổi sang {$ngayGioMoi}.",
                'LichHen',
                $lichHen->id
            );
        }
    }
}
