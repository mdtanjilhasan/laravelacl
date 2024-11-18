<?php

namespace Modules\Acl\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PermissionGroups extends Model
{
    public function permissions()
    {
        return $this->hasMany(Permission::class, 'permission_group_id', 'id');
    }
}
