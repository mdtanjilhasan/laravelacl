<?php

namespace Modules\Acl\Services\Contracts;

interface Readable
{
    public function all(array $columns = ['*'], array $relations = [], array $filters = []);
}
