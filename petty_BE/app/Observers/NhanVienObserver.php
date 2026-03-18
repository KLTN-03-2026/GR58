<?php

namespace App\Observers;

use App\Models\NhanVien;
use App\Models\PhanQuyen;

class NhanVienObserver
{
    /**
     * Handle the NhanVien "creating" event.
     * Tự động gán vai trò mặc định nếu chưa có
     */
    public function creating(NhanVien $nhanVien): void
    {
        // Nếu chưa có phan_quyen_id, tự động gán vai trò dựa trên vai_tro
        if (empty($nhanVien->phan_quyen_id)) {
            // Gán vai trò dựa trên vai_tro
            $roleMap = [
                'Bác sĩ' => 2,      // Bác sĩ
                'Điều dưỡng' => 3,  // Điều dưỡng
                'Lễ tân' => 4,      // Lễ tân
            ];

            $nhanVien->phan_quyen_id = $roleMap[$nhanVien->vai_tro] ?? 2; // Mặc định là Bác sĩ
        }
    }

    /**
     * Handle the NhanVien "created" event.
     * Đảm bảo NhanVien có vai trò sau khi tạo
     */
    public function created(NhanVien $nhanVien): void
    {
        // Nếu vẫn chưa có phan_quyen_id sau khi tạo, update ngay
        if (empty($nhanVien->phan_quyen_id)) {
            $roleMap = [
                'Bác sĩ' => 2,
                'Điều dưỡng' => 3,
                'Lễ tân' => 4,
            ];

            $nhanVien->update(['phan_quyen_id' => $roleMap[$nhanVien->vai_tro] ?? 2]);
        }
    }

    /**
     * Handle the NhanVien "updating" event.
     * Tự động cập nhật vai trò khi thay đổi vai_tro
     */
    public function updating(NhanVien $nhanVien): void
    {
        // Nếu vai_tro thay đổi, cập nhật phan_quyen_id tương ứng
        if ($nhanVien->isDirty('vai_tro')) {
            $roleMap = [
                'Bác sĩ' => 2,
                'Điều dưỡng' => 3,
                'Lễ tân' => 4,
            ];

            $nhanVien->phan_quyen_id = $roleMap[$nhanVien->vai_tro] ?? 2;
        }
    }
}
