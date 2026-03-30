<?php

namespace App\Observers;

use App\Models\LoanDetail;

class LoanDetailObserver
{
    /**
     * Handle the LoanDetail "created" event.
     */
    public function created(LoanDetail $loanDetail): void
    {
        //
    }

    /**
     * Handle the LoanDetail "updated" event.
     */
    public function updated(LoanDetail $detail)
    {
        if (!$detail->wasChanged('returned_at') || $detail->returned_at === null) {
            return;
        }

        $detail->itemUnit()->update([
            'status' => 'available',
        ]);
    }

    /**
     * Handle the LoanDetail "deleted" event.
     */
    public function deleted(LoanDetail $loanDetail): void
    {
        //
    }

    /**
     * Handle the LoanDetail "restored" event.
     */
    public function restored(LoanDetail $loanDetail): void
    {
        //
    }

    /**
     * Handle the LoanDetail "force deleted" event.
     */
    public function forceDeleted(LoanDetail $loanDetail): void
    {
        //
    }
}
