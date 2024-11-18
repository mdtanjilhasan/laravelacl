<?php

namespace Modules\Acl\Services;

use Modules\Acl\Http\Models\Role;
use Modules\Acl\Services\Contracts\Readable;
use Modules\Acl\Services\Contracts\Creatable;
use Modules\Acl\Services\Contracts\Findable;
use Modules\Acl\Services\Contracts\Updatable;

class RolesService implements Readable, Findable, Creatable, Updatable
{
    public function all(array $columns = ['*'], array $relations = [], array $filters = [])
    {
        return Role::select($columns)->oldest('id')->get();
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
        Role::create($data);
    }

    public function show(int $id, array $columns = ['*'], array $relations = [], array $options = ['with_trash' => false, 'only_trash' => false])
    {
        return Role::select($columns)
            ->when(!empty($relations), function ($query) use ($relations) {
                $query->with($relations);
            })
            ->findOrFail($id);
    }

    public function update(array $data, int $id)
    {
        $role = $this->show($id);
        $role->update($data);
    }
}
