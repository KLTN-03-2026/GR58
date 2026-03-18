<?php

namespace App\Observers;

use App\Models\Admin;
use App\Models\PhanQuyen;

class AdminObserver
{
    /**
     * Handle the Admin "creating" event.
     * Tự động gán vai trò Admin (ID = 1) nếu chưa có
     */
    public function creating(Admin $admin): void
    {
        // Nếu chưa có phan_quyen_id, tự động gán vai trò Admin (ID = 1)
        if (empty($admin->phan_quyen_id)) {
            $admin->phan_quyen_id = 1; // Admin mặc định
        }
    }

    /**
     * Handle the Admin "created" event.
     * Đảm bảo Admin có vai trò sau khi tạo
     */
    public function created(Admin $admin): void
    {
        // Nếu vẫn chưa có phan_quyen_id sau khi tạo, update ngay
        if (empty($admin->phan_quyen_id)) {
            $admin->update(['phan_quyen_id' => 1]);
        }
    }
}
