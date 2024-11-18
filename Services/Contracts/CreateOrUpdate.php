<?php

namespace Modules\Acl\Services\Contracts;

interface CreateOrUpdate
{
    public function createOrUpdate(array $conditions, array $data);
}