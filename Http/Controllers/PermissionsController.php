<?php

namespace Modules\Acl\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Acl\Http\Requests\PermissionValidation;
use Modules\Acl\Services\PermissionsService;
use Modules\Acl\Traits\HttpExceptionsTrait;
use Exception;

class PermissionsController extends Controller
{
    use HttpExceptionsTrait;
    protected PermissionsService $service;

    public function __construct(PermissionsService $permissionsService)
    {
        $this->service = $permissionsService;
    }

    public function getPermissions(Request $request)
    {
        try {
            return $this->service->all(['id', 'name'], [], $request->all());
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage());
        }
    }

    public function storePermission(PermissionValidation $request): jsonResponse
    {
        try {
            DB::beginTransaction();
            $this->service->save($request->validated());
            DB::commit();
            return $this->success('Operation successful');
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->fail($exception->getMessage());
        }
    }

    public function getPermissionsGroup(): jsonResponse
    {
        try {
            return $this->success('Permission group list', $this->service->groups());
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage());
        }
    }

    public function editPermission($id): jsonResponse
    {
        try {
            return $this->success('Permission Data', $this->service->show($id, ['id', 'name', 'permission_group_id']));
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage());
        }
    }

    public function getCurrentUserPermissions(Request $request): jsonResponse
    {
        try {
            return $this->success('All Permission Data', $this->service->getAllPermissions($request->all()));
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage());
        }
    }

    public function destroyPermission($id): jsonResponse
    {
        try {
            DB::beginTransaction();
            $this->service->destroy($id);
            DB::commit();
            return $this->success('Permission Deleted Successfully');
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->fail($exception->getMessage());
        }
    }
}
