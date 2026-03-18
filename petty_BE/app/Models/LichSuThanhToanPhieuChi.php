<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class LichSuThanhToanPhieuChi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lich_su_thanh_toan_phieu_chis';

    protected $fillable = [
        'phieu_chi_id',
        'so_tien_thanh_toan',
        'hinh_thuc_thanh_toan',
        'tien_mat',
        'tien_chuyen_khoan',
        'ghi_chu',
        'so_tien_da_tra_truoc_do',
        'so_tien_con_no_sau_thanh_toan',
        'ngay_thanh_toan',
        'admin_id',
        'nhan_vien_id',
        'nguoi_thanh_toan_type',
    ];

    protected $casts = [
        'so_tien_thanh_toan' => 'decimal:2',
        'tien_mat' => 'decimal:2',
        'tien_chuyen_khoan' => 'decimal:2',
        'so_tien_da_tra_truoc_do' => 'decimal:2',
        'so_tien_con_no_sau_thanh_toan' => 'decimal:2',
        'ngay_thanh_toan' => 'datetime',
    ];

    /**
     * Append custom attributes to the model.
     */
    protected $appends = ['hinh_thuc_thanh_toan_label', 'nguoi_thanh_toan_info'];

    /**
     * Boot method để tự động lưu người thanh toán
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lichSu) {
            // Tự động lấy người đăng nhập
            if (Auth::guard('sanctum')->check()) {
                $user = Auth::guard('sanctum')->user();

                if ($user instanceof \App\Models\Admin) {
                    $lichSu->admin_id = $user->id;
                    $lichSu->nguoi_thanh_toan_type = 'Admin';
                } elseif ($user instanceof \App\Models\NhanVien) {
                    $lichSu->nhan_vien_id = $user->id;
                    $lichSu->nguoi_thanh_toan_type = 'NhanVien';
                }
            }

            // Set ngày thanh toán nếu chưa có
            if (!$lichSu->ngay_thanh_toan) {
                $lichSu->ngay_thanh_toan = now();
            }
        });
    }

    /**
     * Relationship với PhieuChi
     */
    public function phieuChi(): BelongsTo
    {
        return $this->belongsTo(PhieuChi::class, 'phieu_chi_id');
    }

    /**
     * Relationship với Admin
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Relationship với NhanVien
     */
    public function nhanVien(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id');
    }

    /**
     * Get label cho hình thức thanh toán
     */
    public function getHinhThucThanhToanLabelAttribute()
    {
        return match ($this->hinh_thuc_thanh_toan) {
            'tien_mat' => '💵 Tiền mặt',
            'chuyen_khoan' => '🏦 Chuyển khoản',
            'ca_hai' => '💳 Cả hai (Tiền mặt + Chuyển khoản)',
            default => $this->hinh_thuc_thanh_toan,
        };
    }

    /**
     * Get thông tin người thanh toán
     */
    public function getNguoiThanhToanInfoAttribute()
    {
        if ($this->admin_id && $this->admin) {
            return [
                'type' => 'Admin',
                'id' => $this->admin->id,
                'name' => $this->admin->ho_ten,
                'email' => $this->admin->email,
            ];
        }

        if ($this->nhan_vien_id && $this->nhanVien) {
            return [
                'type' => 'NhanVien',
                'id' => $this->nhanVien->id,
                'name' => $this->nhanVien->full_name,
                'email' => $this->nhanVien->email,
            ];
        }

        return null;
    }
}
