<?php

namespace App\Observers;

use App\Models\PhieuNhapKho;
use App\Models\PhieuChi;

class PhieuNhapKhoObserver
{
    /**
     * Handle the PhieuNhapKho "created" event.
     */
    public function created(PhieuNhapKho $phieuNhapKho): void
    {
        //
    }

    /**
     * Handle the PhieuNhapKho "updated" event.
     */
    public function updated(PhieuNhapKho $phieuNhapKho): void
    {
        // Nếu thay đổi trạng thái của phiếu nhập, cập nhật luôn trạng thái phiếu chi
        if ($phieuNhapKho->isDirty('trang_thai') && $phieuNhapKho->phieu_chi_id) {
            $phieuChi = PhieuChi::find($phieuNhapKho->phieu_chi_id);
            if ($phieuChi) {
                // Tùy theo logic nghiệp vụ, có thể ánh xạ trạng thái sang phieuChi
                // Ở đây chúng ta tạm thời set trạng thái tương tự (vì logic gốc làm thế)
                // Tuy nhiên trạng thái phieu chi thường có set riêng thông qua so_tien_con_no
                // Nếu trạng thái của phieuNhapKho là 'da_huy' thì huỷ luôn phiếu chi:
                if ($phieuNhapKho->trang_thai === 'da_huy') {
                    $phieuChi->trang_thai = 'con_no'; // Tuỳ vào ENUM được phép
                    // Cần kiểm tra lại logic này. 
                }
            }
        }
    }
}
