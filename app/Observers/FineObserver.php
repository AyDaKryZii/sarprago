<?php

namespace App\Observers;

use App\Events\ActivityLogged;
use App\Models\Fine;

class FineObserver
{
    /**
     * Handle the Fine "created" event.
     */
    public function created(Fine $fine): void
    {
        //
    }

    /**
     * Handle the Fine "updated" event.
     */
    public function updated(Fine $fine): void
    {
        //
    }

    /**
     * Handle the Fine "deleted" event.
     */
    public function deleted(Fine $fine): void
    {
        //
    }

    /**
     * Handle the Fine "restored" event.
     */
    public function restored(Fine $fine): void
    {
        //
    }

    /**
     * Handle the Fine "force deleted" event.
     */
    public function forceDeleted(Fine $fine): void
    {
        //
    }
}
