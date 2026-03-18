<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhanLoaiBaiViet extends Model
{
    protected $table = 'phan_loai_bai_viets';

    protected $fillable = [
        'ten_phan_loai',
        'slug',
        'mo_ta',
    ];

    // Relationships
    public function baiViets()
    {
        return $this->hasMany(BaiViet::class, 'phan_loai_bai_viet_id');
    }
}
