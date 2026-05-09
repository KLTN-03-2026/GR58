<?php

namespace App\Services;

use App\Models\KhachHang;
use App\Models\LichHen;
use Carbon\Carbon;

class ChatbotContextService
{
    public function buildContext(KhachHang $user): string
    {
        $pets = $user->thuCungs()->get();

        if ($pets->isEmpty()) {
            return '';
        }

        $lines = [];
        $lines[] = '---';
        $lines[] = 'THÔNG TIN THÚ CƯNG CỦA KHÁCH HÀNG:';

        foreach ($pets as $pet) {
            $age = $pet->tuoi_thu_cung
                ? Carbon::parse($pet->tuoi_thu_cung)->age . ' tuổi'
                : 'không rõ tuổi';
            $weight = $pet->can_nang ? $pet->can_nang . 'kg' : '';
            $gender = $pet->gioi_tinh ?? '';

            $info = sprintf(
                '- Bé "%s" [thu_cung_id=%d] (%s, %s, %s%s%s)',
                $pet->ten_thu_cung,
                $pet->id,
                $pet->loai_thu_cung ?? 'không rõ loài',
                $pet->giong_thu_cung ?? 'không rõ giống',
                $age,
                $weight ? ', ' . $weight : '',
                $gender ? ', ' . $gender : ''
            );
            $lines[] = $info;
        }

        $lines[] = '';
        $lines[] = 'LỊCH SỬ KHÁM GẦN NHẤT:';

        foreach ($pets as $pet) {
            $phieuKhams = $pet->phieuKhams()
                ->orderByDesc('phieu_khams.created_at')
                ->limit(3)
                ->get();

            if ($phieuKhams->isEmpty()) {
                $lines[] = '- ' . $pet->ten_thu_cung . ': Chưa có lịch sử khám';
                continue;
            }

            foreach ($phieuKhams as $pk) {
                $date = $pk->created_at ? $pk->created_at->format('d/m/Y') : '';
                $diagnosis = $pk->chan_doan ?? 'chưa có chẩn đoán';
                $lines[] = sprintf(
                    '- %s: %s (%s)',
                    $pet->ten_thu_cung,
                    $diagnosis,
                    $date
                );
            }
        }

        $lines[] = '';
        $lines[] = 'LỊCH HẸN SẮP TỚI:';

        $appointments = LichHen::where('khach_hang_id', $user->id)
            ->where('trang_thai', '!=', 'da_huy')
            ->where('ngay_gio', '>=', Carbon::now())
            ->with(['thuCung', 'dichVu'])
            ->orderBy('ngay_gio')
            ->limit(5)
            ->get();

        if ($appointments->isEmpty()) {
            $lines[] = '- Không có lịch hẹn sắp tới';
        } else {
            foreach ($appointments as $appt) {
                $lines[] = sprintf(
                    '- %s - %s - %s',
                    $appt->ngay_gio ? $appt->ngay_gio->format('d/m/Y H:i') : '',
                    $appt->thuCung->ten_thu_cung ?? 'N/A',
                    $appt->dichVu->ten_dich_vu ?? 'Khám tổng quát'
                );
            }
        }

        return implode("\n", $lines);
    }
}
