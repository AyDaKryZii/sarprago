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

        $loan = $detail->loanItem->loan;

        $anyStillBorrowed = $loan->loanItems()
            ->whereHas('loanDetails', fn ($q) =>
                $q->whereNull('returned_at')
            )
            ->exists();

        if (!$anyStillBorrowed) {
            $loan->update([
                'status' => 'returned',
                'returned_at' => now(),
            ]);
        }
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
