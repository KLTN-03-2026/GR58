<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMucDichVu extends Model
{
    use HasFactory;

    // explicit table name is optional because Eloquent will pluralize the model name,
    // but it's kept here for clarity
    protected $table = 'danh_muc_dich_vus';

    // mass assignable fields
    protected $fillable = [
        'ten_nhom',
        'mo_ta',
    ];

    // casts
    protected $casts = [
        'mo_ta' => 'string',
    ];

    // A category (group) can have many services
    public function dichVus()
    {
        return $this->hasMany(DichVu::class, 'danh_muc_id');
    }

    // Existing relationship to Khoa (many-to-many)
    public function khoas()
    {
        return $this->belongsToMany(
            Khoa::class,
            'danh_muc_dich_vu_khoa',
            'danh_muc_dich_vu_id',
            'khoa_id'
        );
    }
}
