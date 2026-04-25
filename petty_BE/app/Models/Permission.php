<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'group',
        'description',
    ];

    /**
     * Relationship: Permission belongs to many Roles (Many-to-Many)
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }
}
