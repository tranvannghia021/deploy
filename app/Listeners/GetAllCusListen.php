<?php

namespace App\Listeners;

use App\Events\LoginEvent;
use App\Jobs\GetAllCusJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GetAllCusListen
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(LoginEvent $event)
    {
        dispatch(new GetAllCusJob($event->shop))->delay(3);
    }
}
