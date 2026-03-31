<?php

namespace App\Models;

use App\Observers\CategoryObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(CategoryObserver::class)]
class Category extends Model
{
    use HasFactory;
    
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    // Accessors
    protected function itemsCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->items()->count(),
        );
    }
}
