<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class KiemKe extends Model
{
    use HasFactory;

    protected $table = 'kiem_kes';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'hang_hoa_id',
        'chi_tiet_phieu_nhap_kho_id',
        'so_luong_he_thong',
        'so_luong_thuc_te',
        'chenh_lech',
        'ly_do',
        'ngay_kiem_ke',
        'admin_id',
        'nhan_vien_id',
        'nguoi_kiem_ke_type',
        'ghi_chu',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'so_luong_he_thong' => 'integer',
        'so_luong_thuc_te' => 'integer',
        'chenh_lech' => 'integer',
        'ngay_kiem_ke' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Append custom attributes to the model.
     *
     * @var array
     */
    protected $appends = ['trang_thai_chenh_lech', 'nguoi_kiem_ke_info'];

    /**
     * Quan hệ với bảng HangHoa
     */
    public function hangHoa(): BelongsTo
    {
        return $this->belongsTo(HangHoa::class, 'hang_hoa_id');
    }

    /**
     * Quan hệ với bảng ChiTietPhieuNhapKho
     */
    public function chiTietPhieuNhapKho(): BelongsTo
    {
        return $this->belongsTo(ChiTietPhieuNhapKho::class, 'chi_tiet_phieu_nhap_kho_id');
    }

    /**
     * Quan hệ với bảng Admin
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Quan hệ với bảng NhanVien
     */
    public function nhanVien(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id');
    }

    /**
     * Tự động tính chênh lệch và gán người kiểm kê khi lưu
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Tự động tính chênh lệch
            $model->chenh_lech = $model->so_luong_thuc_te - $model->so_luong_he_thong;

            // Tự động lấy người đăng nhập
            if (!$model->admin_id && !$model->nhan_vien_id) {
                $user = Auth::user();

                if ($user) {
                    // Kiểm tra xem user là Admin hay NhanVien
                    if ($user instanceof Admin) {
                        $model->admin_id = $user->id;
                        $model->nguoi_kiem_ke_type = 'Admin';
                    } elseif ($user instanceof NhanVien) {
                        $model->nhan_vien_id = $user->id;
                        $model->nguoi_kiem_ke_type = 'NhanVien';
                    } else {
                        // Kiểm tra guard
                        if (Auth::guard('admin')->check()) {
                            $admin = Auth::guard('admin')->user();
                            $model->admin_id = $admin->id;
                            $model->nguoi_kiem_ke_type = 'Admin';
                        } elseif (Auth::guard('nhan_vien')->check()) {
                            $nhanVien = Auth::guard('nhan_vien')->user();
                            $model->nhan_vien_id = $nhanVien->id;
                            $model->nguoi_kiem_ke_type = 'NhanVien';
                        }
                    }
                }
            }
        });

        static::updating(function ($model) {
            // Tự động tính chênh lệch khi cập nhật
            $model->chenh_lech = $model->so_luong_thuc_te - $model->so_luong_he_thong;
        });
    }

    /**
     * Get trạng thái chênh lệch
     */
    public function getTrangThaiChenhLechAttribute()
    {
        if ($this->chenh_lech > 0) {
            return 'Thừa';
        } elseif ($this->chenh_lech < 0) {
            return 'Thiếu';
        }
        return 'Đúng';
    }

    /**
     * Get thông tin người kiểm kê
     */
    public function getNguoiKiemKeInfoAttribute()
    {
        if ($this->admin_id && $this->admin) {
            return [
                'type' => 'Admin',
                'id' => $this->admin_id,
                'ho_ten' => $this->admin->ho_ten,
                'email' => $this->admin->email,
                'anh_dai_dien' => $this->admin->anh_dai_dien,
            ];
        } elseif ($this->nhan_vien_id && $this->nhanVien) {
            return [
                'type' => 'Nhân viên',
                'id' => $this->nhan_vien_id,
                'ho_ten' => $this->nhanVien->full_name,
                'email' => $this->nhanVien->email,
                'anh_dai_dien' => $this->nhanVien->anh_dai_dien,
                'chuc_danh' => $this->nhanVien->chuc_danh,
            ];
        }

        return null;
    }
}
