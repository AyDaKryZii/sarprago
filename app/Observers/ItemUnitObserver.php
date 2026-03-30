<?php

namespace App\Observers;

use App\Models\ItemUnit;
use Illuminate\Support\Facades\DB;

class ItemUnitObserver
{
    /**
     * Handle the ItemUnit "creating" event.
     */
    public function creating(ItemUnit $unit): void
    {
        DB::transaction(function () use ($unit) {

            $lastOrder = ItemUnit::withTrashed()
                ->where('item_id', $unit->item_id)
                ->lockForUpdate()
                ->max('sort_order') ?? 0;

            $prefix = str(
                $unit->item()->value('code_prefix')
            )->trim('-')->upper();

            $unit->sort_order = $lastOrder + 1;
            $unit->unit_code = $prefix . '-' . str_pad($unit->sort_order, 3, 0, STR_PAD_LEFT);
        });
    }
    
    /**
     * Handle the ItemUnit "created" event.
     */
    public function created(ItemUnit $itemUnit): void
    {
        //
    }

    /**
     * Handle the ItemUnit "updated" event.
     */
    public function updated(ItemUnit $itemUnit): void
    {
        //
    }

    /**
     * Handle the ItemUnit "deleted" event.
     */
    public function deleted(ItemUnit $itemUnit): void
    {
        //
    }

    /**
     * Handle the ItemUnit "restored" event.
     */
    public function restored(ItemUnit $itemUnit): void
    {
        //
    }

    /**
     * Handle the ItemUnit "force deleted" event.
     */
    public function forceDeleted(ItemUnit $itemUnit): void
    {
        //
    }
}
