<?php

namespace Modules\Acl\Http\Models;

class Permission extends \Spatie\Permission\Models\Permission
{
    protected $fillable = ['name', 'guard_name', 'permission_group_id'];
}
