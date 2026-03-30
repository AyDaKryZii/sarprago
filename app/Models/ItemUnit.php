<?php

namespace App\Models;

use App\Observers\ItemUnitObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(ItemUnitObserver::class)]
class ItemUnit extends Model
{
    use SoftDeletes;
    protected $table = 'item_units';

    protected $fillable = [
        'item_id',
        'unit_code',
        'sort_order',
        'condition',
        'status',
        'image_path',
        'attributes',
        'notes',
    ];

    protected $casts = [
        'attributes' => 'array',
        'sort_order' => 'integer',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    // Accessors
    protected function fullDisplayName(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->item->name} - {$this->unit_code}",
        );
    }
}
