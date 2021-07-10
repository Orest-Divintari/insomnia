<?php

namespace App\Observers;

use App\Category;
use Illuminate\Support\Str;

class CategoryObserver
{

    /**
     * Handle the category "deleting" event.
     *
     * @param  \App\Category  $category
     * @return void
     */
    public function deleting(Category $category)
    {
        $category->threads->each->delete();
        $category->subCategories->each->delete();
    }

    public function creating(Category $category)
    {
        if (!$category->isRoot()) {
            $category->group_category_id = $category->category->group_category_id;
        }

        $category->slug = Str::slug($category->title);
    }
}