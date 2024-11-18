<?php

namespace Modules\Acl\Services\Contracts;

interface Trashable
{
    public function trash(int|array $ids, array $payload = []);
}
