<?php

namespace Modules\Acl\Services;

use Modules\Acl\Http\Models\Permission;
use Modules\Acl\Http\Models\PermissionGroups;
use Modules\Acl\Services\Contracts\Readable;
use Modules\Acl\Services\Contracts\Creatable;
use Modules\Acl\Services\Contracts\Deletable;
use Modules\Acl\Services\Contracts\Findable;
use Modules\Acl\Services\Contracts\Updatable;

class PermissionsService implements Readable, Creatable, Findable, Updatable, Deletable
{
    public function all(array $columns = ['*'], array $relations = [], array $filters = [])
    {
        $limit = $filters['limit'] ?? 20;
        $search = $filters['search'] ?? '';
        $sorting = $filters['order'] ?? '';

        $permissions = Permission::select($columns)
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . trim($search) . '%');
            })
            ->when($sorting, function ($query) use ($sorting) {
                $query->orderBy('name', $sorting);
            }, function ($query) {
                $query->latest('name');
            });

        if ($limit < 0) {
            $permissions = $permissions->get();
        } else {
            $permissions = $permissions->paginate($limit);
        }

        return $permissions;
    }

    public function save(array $data)
    {
        if (array_key_exists('id', $data)) {
            $id = $data['id'];
            unset($data['id']);
            $this->update($data, $id);
        } else {
            $this->store($data);
        }
    }

    public function store(array $data)
    {
        Permission::create($data);
    }

    public function getAllPermissions(array $data)
    {
        $user = (new UsersService())->show(auth()->id(), ['id', 'email'], ['permissions']);
        return $user->permissions ?? [];

    }

    public function show(int $id, array $columns = ['*'], array $relations = [], array $options = ['with_trash' => false, 'only_trash' => false])
    {
        return Permission::select($columns)->findOrFail($id);
    }

    public function update(array $data, int $id)
    {
        $permission = $this->show($id);
        $permission->update($data);
    }

    public function destroy(int|array $ids, array $payload = [])
    {
        $primaryKey = is_array($ids) ? $ids : [$ids];
        Permission::whereIn('id', $primaryKey)->delete();
    }

    public function groups()
    {
        return PermissionGroups::select('id', 'name')->get();
    }
}
