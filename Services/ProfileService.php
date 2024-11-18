<?php

namespace Modules\Acl\Services;

use Modules\Acl\Services\Contracts\Creatable;
use Modules\Acl\Services\Contracts\Findable;
use Modules\Acl\Services\Contracts\Updatable;

class ProfileService implements Creatable, Updatable, Findable
{
    public function store(array $data)
    {
        getProfileModel()::create($data);
    }

    public function show(int $id, array $columns = ['*'], array $relations = [], array $options = ['with_trash' => false, 'only_trash' => false])
    {
        return getProfileModel()::select($columns)
            ->when(!empty($relations), function ($query) use ($relations) {
                $query->with($relations);
            })
            ->where('user_id', $id)
            ->first();
    }

    public function update(array $data, int $id)
    {
        $profile = $this->profileUpdate($data, $id);
        (new UsersService())->update(['user' => ['email' => $data['email'], 'name' => "$profile->first_name $profile->last_name"]], $id);
        return $profile->image_path;
    }

    public function profileUpdate(array $data, int $id)
    {
        $profile = $this->show($id);
        if ( array_key_exists('image', $data) ) {
            $imagePath = fileUpload($data['image'], "user_$id");
            if ( ! empty( $imagePath ) ) {
                $data['image_avatar'] = $imagePath;
            }
            unset($data['image']);
        }
        $profile->update($data);
        return $profile;
    }
}
