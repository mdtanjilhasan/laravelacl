<?php

use Illuminate\Support\Facades\Route;
use Modules\Acl\Http\Controllers\Auth\LoginController;

Route::middleware('api.json')->group(function () {
    Route::post('/login', [LoginController::class, 'userLogin'])->name('login');

    Route::prefix('dashboard')->middleware(['auth:sanctum', 'verify.token'])->group(function () {

        Route::post('/logout', [LoginController::class, 'customLogout']);
        Route::get('/get-user-permissions', [getPermissionsController(), 'getCurrentUserPermissions']);

        Route::prefix('user-profile')->controller(getProfileController())->group(function () {
            Route::get('/', 'getUserProfile');
            Route::post('/', 'updateUserProfile');
        });

        Route::prefix('permissions')->controller(getPermissionsController())->group(function () {
            Route::get('/', 'getPermissions')->middleware('permission:view_permissions');
            Route::post('/', 'storePermission')->middleware('permission:add_permissions');
            Route::get('/show/{id}', 'editPermission')->middleware('permission:edit_permissions');
            Route::delete('/delete/{id}', 'destroyPermission')->middleware('permission:delete_permissions');
            Route::get('/group', 'getPermissionsGroup');
        });

        Route::prefix('roles')->controller(getRolesController())->group(function () {
            Route::get('/', 'getRoles')->middleware('permission:view_roles');
            Route::post('/', 'storeRole')->middleware('permission:add_roles');
            Route::get('/show/{id}', 'editRole')->middleware('permission:edit_roles');
            Route::delete('/delete/{id}', 'destroyRole')->middleware('permission:delete_roles');
            Route::get('/{id}/permissions', 'getRoleWisePermissions')->middleware('permission:view_permissions|view_roles');
            Route::post('/{id}/permissions', 'storeRoleWisePermissions')->middleware('permission:is_super_admin');
        });

        Route::prefix('users')->controller(getUsersController())->group(function () {
            Route::get('/', 'getUsers')->middleware('permission:view_users');
            Route::post('/', 'storeUser')->middleware('permission:add_users');
            Route::get('/show/{id}', 'editUser')->middleware('permission:edit_users');
            Route::post('/restore-delete', 'destroyUser')->middleware('permission:delete_users');
        });

    });
});
