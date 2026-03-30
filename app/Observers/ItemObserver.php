<?php

namespace App\Observers;

use App\Events\ActivityLogged;
use App\Helpers\LogHelper;
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
        event(new ActivityLogged(
            $item,
            "Added new item: {$item->name} (ID: {$item->id})",
            'Inventory',
        ));
    }

    /**
     * Handle the Item "updated" event.
     */
    public function updated(Item $item): void
    {
        if ($item->wasChanged('code_prefix')) {
            $prefix = str($item->code_prefix)
                ->trim('-')
                ->upper()
                ->replaceMatches('/[^A-Z0-9-]/', '')
                ->replaceMatches('/-+/', '-')
                ->value();

            $item->units()
                ->withTrashed()
                ->update([
                    'unit_code' => DB::raw("CONCAT(" . DB::getPdo()->quote($prefix . '-') . ", sort_order)")
                ]);
        }
        
        $properties = LogHelper::format($item, ['slug']);

        if (empty($properties)) {
            return;
        }

        event(new ActivityLogged(
            $item,
            "Updated item {$item->name} (ID: {$item->id})",
            'Inventory',
            $properties
        ));
    }

    /**
     * Handle the Item "deleted" event.
     */
    public function deleted(Item $item): void
    {
        event(new ActivityLogged(
            $item,
            "Deleted item: {$item->name} (ID: {$item->id})",
            'Inventory',
        ));
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
