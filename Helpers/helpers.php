<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (!function_exists('getProfileModel')) {
    function getProfileModel()
    {
        return config('acl.profile_module.model') ?? \Modules\Acl\Http\Models\UserProfile::class;
    }
}

if (!function_exists('getProfileController')) {
    function getProfileController()
    {
        return config('acl.profile_module.controller') ?? \Modules\Acl\Http\Controllers\ProfileController::class;
    }
}

if (!function_exists('getPermissionsController')) {
    function getPermissionsController()
    {
        return config('acl.permission_controller') ?? \Modules\Acl\Http\Controllers\PermissionsController::class;
    }
}

if (!function_exists('getRolesController')) {
    function getRolesController()
    {
        return config('acl.role_controller') ?? \Modules\Acl\Http\Controllers\RolesController::class;
    }
}

if (!function_exists('getUsersController')) {
    function getUsersController()
    {
        return config('acl.user_controller') ?? \Modules\Acl\Http\Controllers\UsersController::class;
    }
}

if (!function_exists('fileUpload')) {
    function fileUpload($file, $directory = 'files', $extension = 'doc'): ?string
    {
        if (empty($file)) return null;

        if (is_uploaded_file($file)) {
            $name = $file->hashName();
            $path = Storage::disk('public')->putFileAs($directory, $file, $name);
            if (Storage::disk('public')->exists("$directory/$name")) {
                return "storage/$path";
            }
        }

        if (is_string($file)) {
            $fullPath = $directory . '/' . sha1(Str::random()) . "." . $extension;
            Storage::disk('public')->put($fullPath, base64_decode($file), 'public');
            if (Storage::disk('public')->exists($fullPath)) {
                return "storage/$fullPath";
            }
        }

        return null;
    }
}

if (!function_exists('phoneUsaFormat')) {
    function phoneUsaFormat($phone)
    {
        if (empty($phone)) return '';

        $phoneNumber = str_replace(' ', '', $phone);
        $phoneNumber = str_replace('-', '', $phoneNumber);
        $phoneNumber = str_replace('(', '', $phoneNumber);
        $phoneNumber = str_replace(')', '', $phoneNumber);
        $phoneNumber = str_replace('+1', '', $phoneNumber);
        $phoneNumber = str_replace('+880', '', $phoneNumber);

        if (preg_match('/^(\d{3})(\d{3})(\d{4})$/', $phoneNumber, $matches)) {
            return '(' . $matches[1] . ') ' . $matches[2] . '-' . $matches[3];
        }

        return $phoneNumber;
    }
}
