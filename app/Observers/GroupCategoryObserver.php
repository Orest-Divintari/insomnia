<?php

namespace App\Observers;

use App\Models\GroupCategory;
use illuminate\Support\Str;

class GroupCategoryObserver
{
    /**
     * Handle the group category "force deleted" event.
     *
     * @param  \App\GroupCategory  $groupCategory
     * @return void
     */
    public function deleting(GroupCategory $groupCategory)
    {
        $groupCategory->categories->each->delete();
    }

    /**
     * Handle the group category "force deleted" event.
     *
     * @param  \App\GroupCategory  $groupCategory
     * @return void
     */
    public function creating(GroupCategory $groupCategory)
    {
        $groupCategory->slug = Str::slug($groupCategory->title);
    }
}
