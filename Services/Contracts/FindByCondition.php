<?php

namespace Modules\Acl\Services\Contracts;

interface FindByCondition
{
    public function findByCondition(array $condition, array $columns = ['*'], array $relations = [], array $filters = []);
}