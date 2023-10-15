<?php
namespace Alban\LaravelSubusers;

use Alban\LaravelSubusers\Events\DowngradedAsSubuser;
use Alban\LaravelSubusers\Events\UpgradedAsOwner;
use Alban\LaravelSubusers\Listeners\CreateNewOwner;
use Alban\LaravelSubusers\Listeners\RemovePreviousOwner;
use Illuminate\Support;
use Illuminate\Support\Facades\Event;

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

        Event::listen(
            UpgradedAsOwner::class,
            CreateNewOwner::class,
        );

        Event::listen(
            DowngradedAsSubuser::class,
            RemovePreviousOwner::class,
        );
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/subusers.php', 'subusers');
    }
}
