<?php

namespace App\Models;

use App\Observers\ItemObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(ItemObserver::class)]
class Item extends Model
{
    use SoftDeletes;
    protected $table = 'items';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'brand',
        'description',
        'code_prefix',
        'image_path',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(ItemUnit::class);
    }

    // Accessors
    protected function totalStock(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->itemUnits()->count(),
        );
    }

    protected function availableStock(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->units()->where('status', 'available')->count(),
        );
    }
}
