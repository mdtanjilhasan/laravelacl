<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Acl\Http\Models\Role;
use Modules\Acl\Http\Models\Permission;
use Modules\Acl\Http\Models\UserProfile;
use Modules\Acl\Http\Models\PermissionGroups;

class AclDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $order = 1;
        foreach ($this->defaultPermissions() as $group => $permissions) {
            $permissionGroup = PermissionGroups::create(['name' => $group, 's_order' => $order]);
            $order++;
            foreach ($permissions as $name => $label) {
                Permission::firstOrCreate(['name' => $name, 'permission_group_id' => $permissionGroup->id]);
            }
        }

        $input_roles = $this->command->ask('Enter roles in comma separate format.', 'Super Admin,Developer,Editor');
        $roles_array = explode(',', $input_roles);
        $order = 1;
        foreach ($roles_array as $role) {
            $role = Role::firstOrCreate(['name' => trim($role), 's_order' => $order]);
            $order++;
            if (in_array($role->name, ['Super Admin', 'Developer'])) {
                $role->syncPermissions(Permission::all());
            }
        }

        if ($this->command->confirm('Do you wish to import demo data?')) {
            $this->createSeed();
        }

        $this->command->warn('All done :)');
    }

    public function defaultPermissions(): array
    {
        return [
            'Roles' => ['view_roles' => 'View roles list', 'add_roles' => 'Add new role', 'edit_roles' => 'Edit role', 'delete_roles' => 'Delete roles'],
            'Permissions' => ['view_permissions' => 'View permission list', 'add_permissions' => 'Add new permission', 'edit_permissions' => 'Edit permission', 'delete_permissions' => 'Delete permissions'],
            'Users' => ['view_users' => 'View Users List', 'add_users' => 'Add new user', 'edit_users' => 'Edit user', 'trash_users' => 'Trash users', 'restore_users' => 'Restore users', 'delete_users' => 'Delete users']
        ];
    }

    private function createSeed(): void
    {
        $password = Hash::make('password');
        $now = Carbon::now();
        // set 1 start
        $current_user = User::create([
            'name' => 'Mr. Super Admin',
            'email' => 'superadmin@example.com',
            'password' => $password,
            'email_verified_at' => $now,
        ]);

        UserProfile::create([
            'user_id' => $current_user->id,
            'first_name' => 'Mr.',
            'last_name' => 'Super Admin',
        ]);

        $current_user->assignRole(Role::where('name', 'Super Admin')->value('name'));
        // set 1 end

        // set 2 start
        $current_user = User::create([
            'name' => 'Mr. Developer',
            'email' => 'developer@example.com',
            'password' => $password,
            'email_verified_at' => $now,
        ]);

        UserProfile::create([
            'user_id' => $current_user->id,
            'first_name' => 'Mr.',
            'last_name' => 'Developer',
        ]);

        $current_user->assignRole(Role::where('name', 'Developer')->value('name'));
        // set 2 end

        // set 3 start
        $current_user = User::create([
            'name' => 'Mr. Editor',
            'email' => 'editor@example.com',
            'password' => $password,
            'email_verified_at' => $now,
        ]);

        UserProfile::create([
            'user_id' => $current_user->id,
            'first_name' => 'Mr.',
            'last_name' => 'Editor',
        ]);

        $current_user->assignRole(Role::where('name', 'Editor')->value('name'));
        // set 3 end
    }
}
