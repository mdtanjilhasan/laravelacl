<?php

namespace Modules\Acl\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Acl\Http\Requests\UserSoftDeleteValidation;
use Modules\Acl\Http\Requests\UsersValidation;
use Modules\Acl\Services\UsersService;
use Modules\Acl\Traits\HttpExceptionsTrait;
use Exception;

class UsersController extends Controller
{
    use HttpExceptionsTrait;
    protected UsersService $service;

    public function __construct(UsersService $usersService)
    {
        $this->service = $usersService;
    }

    public function getUsers(Request $request)
    {
        try {
            return $this->service->all(['id', 'name', 'email', 'deleted_at'], ['profile:id,user_id,image_avatar', 'roles'], $request->all());
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage());
        }
    }

    public function storeUser(UsersValidation $request): jsonResponse
    {
        try {
            $this->service->save($request->getUserData());
            return $this->success('Operation successful');
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage());
        }
    }

    public function editUser($id): jsonResponse
    {
        try {
            return $this->success('User Data', $this->service->show($id, ['id', 'email'], ['profile:id,user_id,first_name,last_name', 'roles:id,name']));
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage());
        }
    }

    public function destroyUser(UserSoftDeleteValidation $request): jsonResponse
    {
        try {
            $this->service->softDeleteOperation($request->validated());
            return $this->success('Operation successful');
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage());
        }
    }
}
