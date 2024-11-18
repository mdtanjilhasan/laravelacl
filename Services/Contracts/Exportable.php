<?php

namespace Modules\Acl\Services\Contracts;

interface Exportable
{
    public function export(array $filters);
}
