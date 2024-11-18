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
        $input_roles = $this->command->ask('Enter roles in comma separate format.', 'Super Admin,Developer,Editor');
        $roles_array = explode(',', $input_roles);

        $permissionGroups = [];
        foreach ($roles_array as $name) {
            $permissionGroups[] = ['name' => trim($name), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()];
        }

        PermissionGroups::insert($permissionGroups);

        // Seed the default permissions
        foreach ($this->defaultPermissions() as $groupId => $permissions) {
            foreach ($permissions as $name => $label) {
                Permission::firstOrCreate(['name' => $name, 'permission_group_id' => $groupId]);
            }
        }

        foreach ($roles_array as $role) {
            $role = Role::firstOrCreate(['name' => trim($role)]);
            if ($role->name == 'Super Admin' || $role->name == 'Developer') {
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
            1 => ['is_super_admin' => 'Super Admin'],
            2 => ['is_admin' => 'Admin'],
            3 => ['is_developer' => 'Developer'],
            4 => ['view_roles' => 'View roles list', 'add_roles' => 'Add new roles', 'edit_roles' => 'Edit roles', 'delete_roles' => 'Delete roles'],
            5 => ['view_permissions' => 'View permission list', 'add_permissions' => 'Add permissions', 'edit_permissions' => 'Edit permissions', 'edit_permissions_keyword' => 'Edit permissions keyword', 'delete_permissions' => 'Delete permissions'],
            6 => ['view_users' => 'View Users List', 'add_users' => 'Add new user', 'edit_users' => 'Edit users', 'delete_users' => 'Delete users']
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
