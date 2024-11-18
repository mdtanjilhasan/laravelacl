<?php

return [
    'name' => 'Acl',
    'profile_module' => [
        'table_name' => 'user_profile',
        'is_uuid_primary' => false,
        'uuid_column_name' => 'id',
        'controller' => \Modules\Acl\Http\Controllers\ProfileController::class,
        'softdelete' => true,
        'create_table' => true,
        'model' => \Modules\Acl\Http\Models\UserProfile::class
    ],
    'permission_controller' => \Modules\Acl\Http\Controllers\PermissionsController::class,
    'role_controller' => \Modules\Acl\Http\Controllers\RolesController::class,
    'user_controller' => \Modules\Acl\Http\Controllers\UsersController::class,
    'user_softdelete' => true,
    'modify_role_table' => true,
    'modify_users_table' => true,
    'modify_permission_table' => true,
    'create_permission_group_table' => true,
];
