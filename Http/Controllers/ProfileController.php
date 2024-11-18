<?php

namespace Modules\Acl\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Acl\Http\Requests\ProfileValidation;
use Modules\Acl\Services\ProfileService;
use Modules\Acl\Services\UsersService;
use Modules\Acl\Traits\HttpExceptionsTrait;
use Exception;

class ProfileController extends Controller
{
    use HttpExceptionsTrait;
    protected ProfileService $service;

    public function __construct(ProfileService $profileService)
    {
        $this->service = $profileService;
    }

    public function getUserProfile(UsersService $usersService): jsonResponse
    {
        try {
            $userProfile = $usersService->show(auth()->id(), ['id', 'email'], ['profile:id,user_id,first_name,last_name,image_avatar']);
            return $this->success('User Profile Data', $userProfile);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage());
        }
    }

    public function updateUserProfile(ProfileValidation $request): jsonResponse
    {
        try {
            return $this->success('User Profile Updated Successfully.', ['url' => $this->service->update($request->validated(), auth()->id())]);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage());
        }
    }
}
