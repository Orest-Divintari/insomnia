<?php

namespace App\Observers;

use App\Category;

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
}