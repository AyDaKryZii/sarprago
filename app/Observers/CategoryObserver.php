<?php

namespace App\Observers;

use App\Events\ActivityLogged;
use App\Helpers\LogHelper;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryObserver
{
    /**
     * Handle the Category "saving" event.
     */
    public function saving(Category $category): void
    {
        if ($category->isDirty('name')) {
            $category->slug = Str::slug($category->name);
        }
    }
    
    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        event(new ActivityLogged(
            $category,
            "Added new category: {$category->name} (ID: {$category->id})",
            'Inventory',
        ));
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        $properties = LogHelper::format($category, ['slug']);

        if ($category->wasChanged('name')){
            event(new ActivityLogged(
                $category,
                "Updated category from: {$category->name} (ID: {$category->id}) to: {$category->name} (ID: {$category->id})",
                'Inventory',
                $properties
            ));
        }
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        event(new ActivityLogged(
            $category,
            "Deleted category: {$category->name} (ID: {$category->id})",
            'Inventory',
        ));
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        //
    }
}
