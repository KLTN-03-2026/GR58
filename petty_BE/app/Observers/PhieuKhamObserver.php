<?php

namespace App\Observers;

use App\Models\PhieuKham;
use App\Services\ThongBaoService;

class PhieuKhamObserver
{
    public function updated(PhieuKham $phieuKham): void
    {
        $chanDoanChanged = $phieuKham->wasChanged('chan_doan')
            && $phieuKham->getOriginal('chan_doan') === null
            && $phieuKham->chan_doan !== null;

        $ketQuaChanged = $phieuKham->wasChanged('ket_qua_can_lam_sang')
            && $phieuKham->getOriginal('ket_qua_can_lam_sang') === null
            && $phieuKham->ket_qua_can_lam_sang !== null;

        if (!$chanDoanChanged && !$ketQuaChanged) return;

        $lichHen = $phieuKham->lichHen;
        if (!$lichHen) return;

        $khachHangId = $lichHen->khach_hang_id;
        if (!$khachHangId) return;

        $service = app(ThongBaoService::class);
        $service->create(
            $khachHangId,
            'ket_qua_kham',
            'Kết quả khám đã sẵn sàng',
            'Bác sĩ đã hoàn tất ghi kết quả khám cho thú cưng của bạn.',
            'PhieuKham',
            $phieuKham->id
        );
    }
}
