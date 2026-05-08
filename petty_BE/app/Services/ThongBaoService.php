<?php

namespace App\Services;

use App\Models\ThongBao;
use Illuminate\Support\Facades\Log;

class ThongBaoService
{
    public function create(
        int $khachHangId,
        string $loai,
        string $tieuDe,
        string $noiDung,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): ?ThongBao {
        try {
            return ThongBao::create([
                'khach_hang_id' => $khachHangId,
                'loai' => $loai,
                'tieu_de' => $tieuDe,
                'noi_dung' => $noiDung,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);
        } catch (\Exception $e) {
            Log::error('ThongBaoService::create failed', [
                'khach_hang_id' => $khachHangId,
                'loai' => $loai,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
