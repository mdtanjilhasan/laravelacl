<?php

namespace Modules\Acl\Services\Contracts;

interface Deletable
{
    public function destroy(int|array $ids, array $payload = []);
}
