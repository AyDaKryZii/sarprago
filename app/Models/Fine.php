<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fine extends Model
{
    protected $fillable = [
        'loan_id',
        'user_id',
        'amount',
        'reason',
        'status',
        'paid_at',
    ];

    // Relasi ke Peminjaman
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    // Relasi ke User (Peminjam)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fine()
{
    return $this->hasOne(Fine::class);
}
}
