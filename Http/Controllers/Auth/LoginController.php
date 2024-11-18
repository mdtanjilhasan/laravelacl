<?php

namespace Modules\Acl\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Acl\Http\Requests\LoginValidation;
use Modules\Acl\Services\LoginService;
use Modules\Acl\Traits\HttpExceptionsTrait;
use Exception;

class LoginController extends Controller
{
    use HttpExceptionsTrait;
    protected LoginService $service;

    public function __construct(LoginService $loginService)
    {
        $this->service = $loginService;
    }

    public function userLogin(LoginValidation $request): JsonResponse
    {
        try {
            return $this->success('Successfully Logged In', $this->service->login($request->validated()));
        } catch (Exception $exception) {
            $error = $exception->getCode() === 200 ? ['password' => $exception->getMessage()] : [];
            $code = empty($error) ? 500 : 422;
            return $this->fail($exception->getMessage(), $error, $code);
        }
    }

    public function customLogout(): jsonResponse
    {
        try {
            $this->service->logout();
            return $this->success('You have logged out successfully');
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage());
        }
    }
}
