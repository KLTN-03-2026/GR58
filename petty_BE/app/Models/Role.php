<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * Relationship: Role has many Permissions (Many-to-Many)
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermission($permissionName)
    {
        return $this->permissions()->where('name', $permissionName)->exists();
    }

    /**
     * Assign permission to role
     */
    public function givePermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }
        
        return $this->permissions()->syncWithoutDetaching([$permission->id]);
    }

    /**
     * Remove permission from role
     */
    public function revokePermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }
        
        return $this->permissions()->detach($permission->id);
    }
}
