<?php

namespace App\Observers;

use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ItemObserver
{
    /**
     * Handle the Item "creating" event.
     */
    public function creating(Item $item): void
    {
        if ($item->isDirty('name')) {
            $item->slug = Str::slug($item->name);
        }
    }
    /**
     * Handle the Item "created" event.
     */
    public function created(Item $item): void
    {
        //
    }

    /**
     * Handle the Item "updated" event.
     */
    public function updated(Item $item): void
    {
        if (! $item->wasChanged('code_prefix')) {
            return;
        }

        $prefix = str($item->code_prefix)
            ->trim('-')
            ->upper()
            ->replaceMatches('/[^A-Z0-9-]/', '')
            ->replaceMatches('/-+/', '-')
            ->value();

        $item->units()
            ->withTrashed()
            ->update([
                'unit_code' => DB::raw("CONCAT('{$prefix}-', sort_order)")
            ]);
    }

    /**
     * Handle the Item "deleted" event.
     */
    public function deleted(Item $item): void
    {
        //
    }

    /**
     * Handle the Item "restored" event.
     */
    public function restored(Item $item): void
    {
        //
    }

    /**
     * Handle the Item "force deleted" event.
     */
    public function forceDeleted(Item $item): void
    {
        //
    }
}
