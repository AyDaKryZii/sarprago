<?php

namespace App\Observers;

use App\Models\ItemUnit;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;

class LoanObserver
{
    /**
     * Handle the Loan "creating" event.
     */
    public function creating(Loan $loan): void
    {
        DB::transaction(function () use ($loan) {

            $prefix = 'LOAN';
            $year = now()->year;

            $lastNumber = Loan::whereYear('created_at', $year)
                ->lockForUpdate()
                ->selectRaw("MAX(CAST(SUBSTRING_INDEX(loan_code, '-', -1) AS UNSIGNED)) as max_number")
                ->value('max_number') ?? 0;

            $nextNumber = $lastNumber + 1;

            $loan->loan_code = sprintf(
                "%s-%d-%03d",
                $prefix,
                $year,
                $nextNumber
            );
        });
    }
    
    /**
     * Handle the Loan "created" event.
     */
    public function created(Loan $loan): void
    {
        //
    }

    /**
     * Handle the Loan "updated" event.
     */
    public function updated(Loan $loan): void
    {
        //
    }

    /**
     * Handle the Loan "deleted" event.
     */
    public function deleted(Loan $loan): void
    {
        //
    }

    /**
     * Handle the Loan "restored" event.
     */
    public function restored(Loan $loan): void
    {
        //
    }

    /**
     * Handle the Loan "force deleted" event.
     */
    public function forceDeleted(Loan $loan): void
    {
        //
    }
}
