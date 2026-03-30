<?php

namespace App\Observers;

use App\Models\ItemUnit;
use App\Models\LoanItem;
use Illuminate\Support\Facades\DB;

class LoanItemObserver
{
    /**
     * Handle the LoanItem "created" event.
     */
        public function created(LoanItem $loanItem): void
    {
        DB::transaction(function () use ($loanItem) {

            $units = ItemUnit::where('item_id', $loanItem->item_id)
                ->where('status', 'available')
                ->limit($loanItem->qty_request)
                ->lockForUpdate()
                ->get();

            if ($units->count() < $loanItem->qty_request) {
                throw new \Exception('Unit not enough');
            }

            foreach ($units as $unit) {

                $loanItem->loanDetails()->create([
                    'item_unit_id' => $unit->id,
                    'condition_out' => $unit->condition
                ]);

                $unit->update([
                    'status' => 'reserved',
                ]);
            }
        });
    }


    /**
     * Handle the LoanItem "updated" event.
     */
    public function updated(LoanItem $loanItem): void
    {
        //
    }

    /**
     * Handle the LoanItem "deleted" event.
     */
    public function deleted(LoanItem $loanItem): void
    {
        //
    }

    /**
     * Handle the LoanItem "restored" event.
     */
    public function restored(LoanItem $loanItem): void
    {
        //
    }

    /**
     * Handle the LoanItem "force deleted" event.
     */
    public function forceDeleted(LoanItem $loanItem): void
    {
        //
    }
}
