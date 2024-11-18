<?php

namespace Modules\Acl\Services\Contracts;

interface Updatable
{
    public function update(array $data, int $id);
}
