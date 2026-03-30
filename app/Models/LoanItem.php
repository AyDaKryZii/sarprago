<?php

namespace App\Models;

use App\Observers\LoanItemObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(LoanItemObserver::class)]

class LoanItem extends Model
{
    protected $table = 'loan_items';

    protected $fillable = [
        'loan_id',
        'item_id',
        'qty_request',
        'qty_approved',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function loanDetails(): HasMany
    {
        return $this->hasMany(LoanDetail::class);
    }
}
