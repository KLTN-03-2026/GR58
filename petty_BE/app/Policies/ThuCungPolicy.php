<?php

namespace App\Policies;

use App\Models\KhachHang;
use App\Models\ThuCung;

class ThuCungPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(KhachHang $user): bool
    {
        // any authenticated customer may view their own pets
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(KhachHang $user, ThuCung $thuCung): bool
    {
        return $thuCung->khach_hang_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(KhachHang $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(KhachHang $user, ThuCung $thuCung): bool
    {
        return $thuCung->khach_hang_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(KhachHang $user, ThuCung $thuCung): bool
    {
        return $thuCung->khach_hang_id === $user->id;
    }
}
