<?php

namespace App\Models;

use App\Models\Role\{Role, Permission};
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /******************************************************************************************************************/

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'timothy_role.users_roles');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'timothy_role.users_permissions');
    }

    /******************************************************************************************************************/

    /**
     *  Check if User has a given Role | Set in Middleware
     *
     * @param array ...$roles
     * @return bool
     */
    public function hasRole(...$roles)
    {
        foreach ($roles as $role):
            if ($this->roles->contains('name', $role)):
                return true;
            endif;
        endforeach;

        return false;
    }

    /**
     *  Check if User has a given Permission | Set in Middleware
     *
     * @param $permission
     * @return bool
     */
    public function hasPermissionTo($permission)
    {
        return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission);
    }

    /**
     *  User with given Role can set / attach given Permission
     *
     * @param array ...$permissions
     * @return $this
     */
    public function givePermissionsTo(...$permissions)
    {
        $permissions = $this->getAllPermissions(array_flatten($permissions));

        if ($permissions === null):
            return $this;
        endif;

        $this->permissions()->saveMany($permissions);

        return $this;
    }

    /**
     *  User with given Role can unset / delete given Permission
     *
     * @param array ...$permissions
     * @return $this
     */
    public function withdrawPermissionsTo(...$permissions)
    {
        $permissions = $this->getAllPermissions(array_flatten($permissions));

        $this->permissions()->detach($permissions);

        return $this;
    }

    /**
     *  User with given Role can update given Permission
     *
     * @param array ...$permissions
     * @return $this
     */
    public function updatePermissions(...$permissions)
    {
        $this->permissions()->detach();

        return $this->givePermissionsTo($permissions);
    }

    /**
     *  User with given Role can give all Permissions
     *
     * @param array $permissions
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    protected function getAllPermissions(array $permissions)
    {
        return Permission::whereIn('name', $permissions)->get();
    }

    /**
     *  Check if User has set Permission
     *
     * @param $permission
     * @return bool
     */
    protected function hasPermission($permission)
    {
        return (bool) $this->permissions->where('name', $permission->name)->count();
    }

    /**
     *  Check if User has Permission through given Role
     *
     * @param $permission
     * @return bool
     */
    protected function hasPermissionThroughRole($permission)
    {
        foreach ($permission->roles as $role):
            if ($this->roles->contains($role)):
                return true;
            endif;
        endforeach;

        return false;
    }

    /******************************************************************************************************************/
}
