<?php

namespace Modules\Acl\Services\Contracts;

interface Importable
{
    public function import(array $filters);
}
