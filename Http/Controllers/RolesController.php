<?php

namespace Modules\Acl\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Acl\Http\Requests\RolePermissionValidation;
use Modules\Acl\Http\Requests\RolesValidation;
use Modules\Acl\Services\RolePermissionService;
use Modules\Acl\Services\RolesService;
use Modules\Acl\Traits\HttpExceptionsTrait;
use Exception;

class RolesController extends Controller
{
    use HttpExceptionsTrait;
    protected RolesService $service;

    public function __construct(RolesService $rolesService)
    {
        $this->service = $rolesService;
    }

    public function getRoles()
    {
        try {
            return $this->service->all(['id', 'name']);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage());
        }
    }

    public function storeRole(RolesValidation $request): jsonResponse
    {
        try {
            DB::beginTransaction();
            $this->service->save($request->validated());
            DB::commit();
            return $this->success('Operation Successful.');
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->fail($exception->getMessage());
        }
    }

    public function editRole($id): jsonResponse
    {
        try {
            return $this->success('Role data', $this->service->show($id, ['id', 'name']));
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage());
        }
    }

    public function getRoleWisePermissions($id, RolePermissionService $service): jsonResponse
    {
        try {
            return $this->success('Role wise permissions list.', $service->show($id, ['id', 'name'], ['permissions:id,name,permission_group_id']));
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage());
        }
    }

    public function storeRoleWisePermissions(RolePermissionValidation $request, RolePermissionService $service): JsonResponse
    {
        try {
            $data = $request->validated();
            DB::beginTransaction();
            $service->update($data['permissions'], $data['id']);
            DB::commit();
            return $this->success('Permissions Updated');
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->fail($exception->getMessage());
        }
    }
}
