<?php
 
namespace Alban\LaravelSubusers\Listeners;

use Alban\LaravelSubusers\Events\DowngradedAsSubuser;

class RemovePreviousOwner
{
    /**
     * Handle the event.
     */
    public function handle(DowngradedAsSubuser $event): void
    {
        // Access the order using $event->order...
    }
}
