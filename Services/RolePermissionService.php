<?php

namespace Modules\Acl\Services;

use Modules\Acl\Http\Models\PermissionGroups;
use Modules\Acl\Services\Contracts\Findable;
use Modules\Acl\Services\Contracts\Updatable;

class RolePermissionService implements Findable, Updatable
{
    public function show(int $id, array $columns = ['*'], array $relations = [], array $options = ['with_trash' => false, 'only_trash' => false]): array
    {
        return [
            'permissions' => (new RolesService())->show($id, $columns, $relations),
            'groups' => PermissionGroups::select($columns)->with($relations)->oldest('id')->get()
        ];
    }

    public function update(array $data, int $id)
    {
        $role = (new RolesService())->show($id);

        if (strtolower($role->name) === 'super admin') {
            $role->syncPermissions( (new PermissionsService())->all( ['*'], [], ['limit' => -1] ) );
        } else {
            $role->syncPermissions($data);
        }
    }
}
