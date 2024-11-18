<?php

namespace Modules\Acl\Services\Contracts;

interface Insertable
{
    public function insertAll(array $data);
}