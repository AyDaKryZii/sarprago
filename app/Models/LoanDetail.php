<?php

namespace App\Models;

use App\Observers\LoanDetailObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([LoanDetailObserver::class])]
class LoanDetail extends Model
{
    protected $table = 'loan_details';

    protected $fillable = [
        'loan_item_id',
        'item_unit_id',
        'condition_out',
        'condition_in',
        'returned_at',
    ];

    protected $cast = [
        'returned_at' => 'datetime',
    ];

    public function loanItem(): BelongsTo
    {
        return $this->belongsTo(LoanItem::class);
    }

    public function itemUnit(): BelongsTo
    {
        return $this->belongsTo(ItemUnit::class);
    }
}
