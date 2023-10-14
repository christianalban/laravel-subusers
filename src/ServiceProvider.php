<?php
namespace Alban\LaravelOwners;

use Illuminate\Support;

class ServiceProvider extends Support\ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/owners.php' => config_path('owners.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/owners.php', 'owners');
    }
}
