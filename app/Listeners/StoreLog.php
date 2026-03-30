<?php

namespace App\Listeners;

use App\Events\ActivityLogged;
use App\Models\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StoreLog
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ActivityLogged $event): void
    {
        ActivityLog::log($event->subject, $event->description, $event->logName, $event->properties);
    }
}
