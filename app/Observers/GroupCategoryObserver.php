<?php

namespace App\Observers;

use App\GroupCategory;

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
}