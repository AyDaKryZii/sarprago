<?php

namespace App\Models;

use App\Observers\LoanObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[ObservedBy(LoanObserver::class)]
class Loan extends Model
{
    protected $table = 'loans';

    protected $fillable = [
        'loan_code',
        'user_id',
        'approved_by',
        'borrowed_at',
        'due_at',
        'returned_at',
        'status',
        'reason',
        'admin_note',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function fine(): HasOne
    {
        return $this->hasOne(Fine::class, 'loan_id');
    }

    public function loanItems(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }

    // Accessors
    protected function isOverdue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'borrowed' && now()->gt($this->due_at),
        );
    }

    protected function isPartiallyApproved(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'partially_approved',
        );
    }

    protected function isPartialReturn(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->status !== 'borrowed') return false;

                // Ngambil semua item loan detail yang sudah di kembalikan 
                // dan diambil via eager load kalau ada supaya gk kena N+1 kalau gk perlu
                // Pakai null safe operator juga buat jaga-jaga
                return $this->loanItems->contains(function ($item) {
                    return $item->loanDetails->contains(fn ($detail) => $detail->returned_at !== null);
                });
            },
        );
    }

    protected function returnProgress(): Attribute
    {
        return Attribute::make(
            get: function () {
                $details = $this->loanItems->flatMap->loanDetails;
                
                $total = $details->count();
                $returned = $details->whereNotNull('returned_at')->count();

                return [
                    'total' => $total,
                    'returned' => $returned,
                    'string' => "{$returned} / {$total} Unit",
                    'percentage' => $total > 0 ? round(($returned / $total) * 100) : 0,
                ];
            }
        );
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->is_overdue) return 'Overdue';
                if ($this->is_partial_return) return 'Borrowed (Partial Return)';
                return ucwords(str_replace('_', ' ', $this->status));
            },
        );
    }
}