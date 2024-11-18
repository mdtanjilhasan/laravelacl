<?php

namespace Modules\Acl\Services\Contracts;

interface Findable
{
    public function show(int $id, array $columns = ['*'], array $relations = [], array $options = ['with_trash' => false, 'only_trash' => false]);
}
