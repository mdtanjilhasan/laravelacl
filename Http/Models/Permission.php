<?php

namespace Modules\Acl\Http\Models;

class Permission extends \Spatie\Permission\Models\Permission
{
    protected $fillable = ['name', 'label', 'guard_name', 'permission_group_id'];
}
