Laravel 9 ACL Using Laravel permission and sanctum
==================================================
-------------------------------------------

### This package is for access control list. Here you can manage users

- Login
- Logout
- Permission
- Role
- Profile

# Let's get started

------------------------------

[comment]: <> (markdown-header is for bitbucket scrollspy)

- [Dependencies](#markdown-header-dependencies)
- [Installation](#markdown-header-installation)
    - [Method 1](#markdown-header-method-1)
    - [Method 2](#markdown-header-method-2)



# Dependencies

--------------------------------
- PHP 8.0
- Laravel 9
- Spatie Laravel permission
- Laravel Sanctum
- Cviebrock Sluggable


# Installation

-------------------------------

There are two methods you can install this package inside your laravel application.

- [Method 1](#markdown-header-method-1)
- [Method 2](#markdown-header-method-2)
- [Back to menu](#markdown-header-lets-get-started)

# Method 1

--------------------------------

### From local path.

1. First you need to download ```1.0.9``` branch in your local machine.
2. Then make ```modules``` folder in root of your laravel project.
3. Inside ```modules``` folder create another folder called ```acl```
4. Then paste all folders from your downloaded folder to ```modules/acl```

### After  ```require-dev``` array in your laravel root ```composer.json``` file add this

```bash
"repositories": [
    {
        "type": "path",
        "url": "modules/acl",
        "options": {
            "symlink": true
        }
    }
],
```

### Then run this command

```bash
composer require modules/acl:dev-main
```

### Manually add the service provider in your ```config/app.php``` file's ```providers``` array

```bash
Spatie\Permission\PermissionServiceProvider::class,
Modules\Acl\Providers\AclServiceProvider::class,
```

### Then run this command

```bash
composer dump-autoload
```

### Then run this command

```bash
php artisan vendor:publish
```

**Spatie Permission publish must be before ACL publish**

**Choose ```--provider="Spatie\Permission\PermissionServiceProvider"```**

**Then you need to publish ```acl-config``` tag**

This will create configuration file to this ```config/acl.php``` path. You can modify some configuration here. **If you don't want to change anything you can skip this ```tag publishing``` step.**

**Choose ```--provider="Modules\Acl\Providers\AclServiceProvider"```**

### Add this in ```$routeMiddleware``` array of ```app/Http/Kernel.php``` file

```bash
'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
'api.json' => \Modules\Acl\Http\Middleware\JsonMiddleware::class,
'verify.token' => \Modules\Acl\Http\Middleware\TokenValidationMiddleware::class,
```

### Then add this line in ```database/seeders/DatabaseSeeder.php``` file inside ```run``` method

```bash
$this->call(AclDatabaseSeeder::class);
```

### * Notice:
##### ```create_user_profile_table.php``` this is our default profile table. You are free to delete this if profile already exists


### Import this in your ```app/Models/User.php``` file

```bash
use Spatie\Permission\Traits\HasRoles;
use Modules\Acl\Traits\SoftDeletes;
```

### Add these traits in your ```app/Models/User.php``` file

```bash
use HasRoles, SoftDeletes;
```

### Add ```deleted_by``` in your User Models ```$fillable``` array AND ```email_verified_at``` in your ```$hidden``` array. Then add this in your User Model

```bash
    protected static function boot()
    {
        parent::boot();
        
        static::restoring(function ($model) {
            $model->deleted_by = null;
        });
    }
```

### If you want to fresh installation run this command

```bash
php artisan migrate:fresh --seed
```

### Otherwise run these commands

```bash
php artisan migrate
php artisan db:seed --class=AclDatabaseSeeder
```

#### If using Docker on MAC OS, need to use ```'${VITE_PORT:-5173}:${VITE_PORT:-5173}'``` Port number can be changed. Example:

```bash
app:
    build:
      args:
        user: www
        uid: 1000
      context: ./
      dockerfile: Dockerfile
    image: laravel-image
    container_name: laravel-app
    ports:
      - '${VITE_PORT:-5173}:${VITE_PORT:-5173}'
    restart: unless-stopped
    working_dir: /var/www/
    depends_on:
      - mysql
```

### * You need to rebuild your container in Docker


--------------------------------------------------------------------------------------------------------------------------------------------------------

========================================================================================================================================================

--------------------------------------------------------------------------------------------------------------------------------------------------------

# Method 2

--------------------------------------------

- [Back to menu](#markdown-header-lets-get-started)

### Form git url

inside url you need to give your git ```username:apppassword```

### After  ```require-dev``` array add this in your laravel root ```composer.json``` file

```bash
"repositories":[
  {
    "type": "vcs",
    "url": "https://username:apppassword@bitbucket.org/your-repo/your-project.git"
  }
],
```

### Then run this command

```bash
composer require modules/acl:1.0.9.x-dev
```

### Manually add the service providers in your ```config/app.php``` file's ```providers``` array

```bash
Spatie\Permission\PermissionServiceProvider::class,
Modules\Acl\Providers\AclServiceProvider::class,
```

### Then run this command

```bash
composer dump-autoload
```

### Then run this command

```bash
php artisan vendor:publish
```

**Spatie Permission publish must be before ACL publish**

**Choose ```--provider="Spatie\Permission\PermissionServiceProvider"```**

**Then you need to publish ```acl-config``` tag**

This will create configuration file to this ```config/acl.php``` path. You can modify some configuration here. **If you don't want to change anything you can skip this ```tag publishing``` step.**

**Choose ```--provider="Modules\Acl\Providers\AclServiceProvider"```**

### Add this in ```$routeMiddleware``` array of ```app/Http/Kernel.php``` file

```bash
'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
'api.json' => \Modules\Acl\Http\Middleware\JsonMiddleware::class,
'verify.token' => \Modules\Acl\Http\Middleware\TokenValidationMiddleware::class,
```

### Add this line to ```database/seeders/DatabaseSeeder.php``` file's ```run``` method

```bash
$this->call(AclDatabaseSeeder::class);
```

### * Notice:
##### create_user_profile_table.php this is our default profile table. You are free to delete this if profile already exists

### Import this in your ```app/Models/User.php``` file

```bash
use Spatie\Permission\Traits\HasRoles;
use Modules\Acl\Traits\SoftDeletes;
```

### Add these traits in your ```app/Models/User.php``` file

```bash
use HasRoles, SoftDeletes;
```
### Add ```deleted_by``` in your User Models ```$fillable``` array AND ```email_verified_at``` in your ```$hidden``` array. Then add this in your User Model

```bash
    protected static function boot()
    {
        parent::boot();
        
        static::restoring(function ($model) {
            $model->deleted_by = null;
        });
    }
```
### If you want to fresh installation run this command

```bash
php artisan migrate:fresh --seed
```

### Otherwise run these commands

```bash
php artisan migrate
php artisan db:seed --class=AclDatabaseSeeder
```

#### If using Docker on MAC OS, need to use ```'${VITE_PORT:-5173}:${VITE_PORT:-5173}'``` Port number can be changed. Example:

```bash
app:
    build:
      args:
        user: www
        uid: 1000
      context: ./
      dockerfile: Dockerfile
    image: laravel-image
    container_name: laravel-app
    ports:
      - '${VITE_PORT:-5173}:${VITE_PORT:-5173}'
    restart: unless-stopped
    working_dir: /var/www/
    depends_on:
      - mysql
```

### * You need to rebuild your container in Docker

## If you want to enable Social Login

-----------------------

```bash
composer require laravel/socialite
```

### Add this in your ```Config/services.php``` file

```bash
'google' => [
    'client_id'     	=> env('GOOGLE_CLIENT_ID'),
    'client_secret' 	=> env('GOOGLE_CLIENT_SECRET'),
    'domains'       	=> env('GOOGLE_EMAIL_DOMAIN', 'yourdomain.edu'),
    'redirect'      	=> env('APP_URL').'/login/google/callback'
],
```

#### Developers
- Md. Tanjil Hasan
