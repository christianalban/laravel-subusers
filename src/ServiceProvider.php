<?php
namespace Alban\LaravelSubusers;

use Illuminate\Support;

class ServiceProvider extends Support\ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/subusers.php' => config_path('subusers.php'),
        ], 'subusers-config');

        $this->publishes([
            __DIR__.'/database/migrations/' => database_path('migrations')
        ], 'subusers-migrations');

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/subusers-config.php', 'subusers');
    }
}
