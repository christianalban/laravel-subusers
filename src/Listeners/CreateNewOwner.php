<?php
 
namespace Alban\LaravelSubusers\Listeners;

use Alban\LaravelSubusers\Events\UpgradedAsOwner;

class CreateNewOwner
{
    /**
     * Handle the event.
     */
    public function handle(UpgradedAsOwner $event): void
    {
        // Access the order using $event->order...
    }
}
