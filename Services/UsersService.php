<?php

namespace Modules\Acl\Services;

use App\Models\User;
use Modules\Acl\Services\Contracts\Readable;
use Modules\Acl\Services\Contracts\Creatable;
use Modules\Acl\Services\Contracts\Deletable;
use Modules\Acl\Services\Contracts\Restorable;
use Modules\Acl\Services\Contracts\Findable;
use Modules\Acl\Services\Contracts\Trashable;
use Modules\Acl\Services\Contracts\Updatable;

class UsersService implements Readable, Creatable, Findable, Updatable, Trashable, Deletable, Restorable
{
    public function all(array $columns = ['*'], array $relations = [], array $filters = [])
    {
        $term = array_key_exists('search', $filters) ? trim($filters['search']) : '';
        $isTrash = isset($filters['active']);
        $limit = $filters['limit'] ?? 20;
        $order = isset($filters['order']) && $filters['order'] === 'asc' ? 'ASC' : 'DESC';
        $roleId = $filters['role'] ?? '';
        $column = '';
        if (isset($filters['columnIndex'])) {
            switch ($filters['columnIndex']) {
                case 1:
                    $column = 'name';
                    break;
                case 2:
                    $column = 'email';
                    break;
            }
        }

        $users = User::select($columns)
            ->when(!empty($relations), function ($query) use ($relations, $roleId) {
                if ($roleId && in_array('roles', $relations)) {
                    $query->whereHas("roles", function ($q) use ($roleId) {
                        $q->where("id", $roleId);
                    })->with($relations);
                } else {
                    $key = array_search('roles', $relations);
                    unset($relations[$key]);
                    $query->with($relations);
                }
            })
            ->when($term, function ($query, $term) {
                $query->where(function ($searchQuery) use ($term) {
                    $searchQuery->where('name', 'LIKE', '%' . $term . '%')->orWhere('email', 'LIKE', '%' . $term . '%');
                });
            })
            ->when($column, function ($query) use ($column, $order) {
                $query->orderBy($column, $order);
            }, function ($query) {
                $query->oldest('name');
            })
            ->when($isTrash, function ($query) {
                $query->onlyTrashed();
            });

        if ($limit < 0) {
            $users = $users->get();
        } else {
            $users = $users->paginate($limit);
        }

        return $users;
    }

    public function store(array $data)
    {
        return User::create($data);
    }

    public function show(int $id, array $columns = ['*'], array $relations = [], array $options = ['with_trash' => false, 'only_trash' => false])
    {
        return User::select($columns)
            ->when(!empty($relations), function ($query) use ($relations) {
                $query->with($relations);
            })
            ->when(!empty($options) && $options['with_trash'], function ($query) {
                $query->withTrashed();
            })
            ->when(!empty($options) && $options['only_trash'], function ($query) {
                $query->onlyTrashed();
            })
            ->findOrFail($id);
    }

    public function update(array $data, int $id)
    {
        $user = $this->show($id, ['id', 'email'], ['profile:id,user_id,first_name,last_name']);
        $user->update($data['user']);
        return $user;
    }

    public function save(array $data)
    {
        $roleId = $data['profile']['role_id'];
        unset($data['profile']['role_id']);

        $profileService = new ProfileService();

        if (array_key_exists('user_id', $data['user'])) {
            $id = $data['user']['user_id'];
            unset($data['user']['user_id']);
            $user = $this->update($data, $id);
            $profileService->profileUpdate($data['profile'], $user->id);
        } else {
            $user = $this->store($data['user']);
            $profileData = $data['profile'];
            $profileData['user_id'] = $user->id;
            $profileService->store($profileData);
        }

        $this->roleAssignment($user, $roleId);
    }

    private function roleAssignment($user, $id)
    {
        $role = (new RolesService())->show($id, ['id', 'name', 'guard_name']);
        $user->syncRoles($role->name);
    }

    public function trash(array|int $ids, array $payload = [])
    {
        User::destroy($ids);
    }

    public function destroy(array|int $ids, array $payload = [])
    {
        User::when(config('acl.user_softdelete'), function ($query) {
            $query->onlyTrashed();
        })
        ->whereIn('id', $ids)
        ->get()
        ->each(function ($user) {
            $user->forceDelete();
        });
    }

    public function restore(array|int $ids, array $payload = [])
    {
        User::when(config('acl.user_softdelete'), function ($query) {
            $query->onlyTrashed();
        })
        ->whereIn('id', $ids)
        ->get()
        ->each(function ($user) {
            $user->restore();
        });
    }

    public function softDeleteOperation(array $data)
    {
        $ids = is_array($data['id']) ? $data['id'] : [$data['id']];
        switch ($data['type']) {
            case 'trash':
                $this->trash($ids);
                break;
            case 'restore':
                $this->restore($ids);
                break;
            case 'delete':
                $this->destroy($ids);
                break;
        }
    }
}
