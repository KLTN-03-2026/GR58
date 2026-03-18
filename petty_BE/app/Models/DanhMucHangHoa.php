<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMucHangHoa extends Model
{
    use HasFactory;

    protected $table = 'danh_muc_hang_hoas';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'ten_danh_muc_hang_hoa',
        'mo_ta',
    ];

    /**
     * Get the products in this category.
     */
    public function hangHoas()
    {
        return $this->hasMany(\App\Models\HangHoa::class, 'danh_muc_hang_hoa_id');
    }
}
