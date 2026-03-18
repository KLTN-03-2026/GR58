<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Admin extends Model
{
    use HasApiTokens, Notifiable;
    protected $fillable = [
        'ho_ten',
        'anh_dai_dien',
        'mat_khau',
        'email',
        'so_dien_thoai',
        'dia_chi',
        'trang_thai',
        'phan_quyen_id',
    ];

    protected $hidden = [
        'mat_khau',
    ];

    protected $casts = [
        'trang_thai' => 'integer',
    ];

    /**
     * Quan hệ với bảng KiemKe
     */
    public function kiemKes()
    {
        return $this->hasMany(KiemKe::class, 'admin_id');
    }

    /**
     * Quan hệ với bảng PhieuChi
     */
    public function phieuChis()
    {
        return $this->hasMany(PhieuChi::class, 'admin_id');
    }

    /**
     * Quan hệ với bảng LichDangKy
     */
    public function lichDangKys()
    {
        return $this->hasMany(LichDangKy::class, 'admin_id');
    }

    /**
     * Quan hệ với bảng PhanQuyen
     */
    public function phanQuyen()
    {
        return $this->belongsTo(PhanQuyen::class, 'phan_quyen_id');
    }

    /**
     * Kiểm tra xem admin có quyền không
     */
    public function hasPermission($permission)
    {
        if (!$this->phanQuyen) {
            return false;
        }
        return $this->phanQuyen->hasPermission($permission);
    }
}
