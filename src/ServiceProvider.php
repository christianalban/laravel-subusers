<?php
namespace Alban\LaravelOwners;

use Illuminate\Support;

class ServiceProvider extends Support\ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/owners.php' => config_path('owners.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/owners.php', 'owners');
    }
}
