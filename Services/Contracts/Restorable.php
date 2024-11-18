<?php

namespace Modules\Acl\Services\Contracts;

interface Restorable
{
    public function restore(int|array $ids, array $payload = []);
}
