<?php
namespace Christian\LaravelOwners;

use Illuminate\Support\ServiceProvider;

class LaravelOwnersServiceProvider extends ServiceProvider
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
