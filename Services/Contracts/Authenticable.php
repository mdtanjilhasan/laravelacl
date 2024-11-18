<?php

namespace Modules\Acl\Services\Contracts;

interface Authenticable
{
    public function login(array $credentials);

    public function logout();
}
