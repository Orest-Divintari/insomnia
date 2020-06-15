<?php

use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $categories = [
            'macOS' => ['macOS catalina', 'macOS lion'],
            'iOS' => ['iOS13', 'iOS14'],
            'macBook' => ['macBook pro', 'macBook air'],
            'General' => [],
            'Discussion' => [],

        ];
        $groups = [
            'software',
            'hardware',
        ];
        foreach ($categories as $category => $childrenCategories) {

            $cat = factory('App\Category')->create(['title' => $category]);

            if ($childrenCategories) {
                foreach ($childrenCategories as $childrenCategory) {
                    $childrenCategory = factory('App\Category')->create(['parent_id' => $cat->id, 'title' => $childrenCategory]);
                    factory('App\Thread', 2)->create(['category_id' => $childrenCategory->id])
                        ->each(function ($thread) {factory('App\Reply', 3)
                                ->create(['repliable_id' => $thread->id, 'repliable_type' => 'App\Thread']);});
                }
            } else {
                factory('App\Thread', 2)->create(['category_id' => $cat->id])
                    ->each(function ($thread) {factory('App\Reply', 3)
                            ->create(['repliable_id' => $thread->id, 'repliable_type' => 'App\Thread']);});
            }

        }

    }
}