<?php

namespace Modules\Acl\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class AclServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'acl';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->registerMigrations();
        $this->registerSeeders();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/acl.php' => config_path('acl.php'),
        ], "$this->moduleNameLower-config");
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Register MigrationsS.
     *
     * @return void
     */
    public function registerMigrations()
    {
        $migrationPath = __DIR__ . '/../Database/Migrations';

        if (config('acl.modify_users_table')) {
            $this->publishes([
                "$migrationPath/modify_users_table.php.stub" => $this->getMigrationFileName('modify_users_table.php'),
            ], "$this->moduleNameLower-migrations");
        }

        if (config('acl.create_permission_group_table')) {
            $this->publishes([
                "$migrationPath/create_permission_groups_table.php.stub" => $this->getMigrationFileName('create_permission_groups_table.php'),
            ], "$this->moduleNameLower-migrations");
        }

        if (config('acl.profile_module.create_table')) {
            $this->publishes([
                "$migrationPath/create_user_profile_table.php.stub" => $this->getMigrationFileName('create_user_profile_table.php'),
            ], "$this->moduleNameLower-migrations");
        }

        if (config('acl.modify_permission_table')) {
            $this->publishes([
                "$migrationPath/modify_permissions_table.php.stub" => $this->getMigrationFileName('modify_permissions_table.php'),
            ], "$this->moduleNameLower-migrations");
        }

        if (config('acl.modify_role_table')) {
            $this->publishes([
                "$migrationPath/modify_roles_table.php.stub" => $this->getMigrationFileName('modify_roles_table.php'),
            ], "$this->moduleNameLower-migrations");
        }
    }

    /**
     * Register MigrationsS.
     *
     * @return void
     */
    public function registerSeeders()
    {
        $seedersFileName = 'AclDatabaseSeeder.php';

        $this->publishes([
            __DIR__ . '/../Database/Seeders/AclDatabaseSeeder.php' => $this->app->databasePath() . "/seeders/$seedersFileName"
        ], "$this->moduleNameLower-seeder");
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @return string
     */
    protected function getMigrationFileName($migrationFileName)
    {
        $databasePath = $this->app->databasePath();
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make($databasePath . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem, $migrationFileName) {
                return $filesystem->glob($path . '*_' . $migrationFileName);
            })
            ->push("$databasePath/migrations/{$timestamp}_$migrationFileName")
            ->first();
    }
}
