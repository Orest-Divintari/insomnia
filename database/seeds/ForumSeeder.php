<?php

use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    const NUM_OF_REPLIES = 10;
    const NUM_OF_THREADS = 10;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $groups = [
            'Macs' => [
                'macOS' => ['macOS catalina', 'macOS lion', 'macOS big Sur'],
                'notebooks' => ['macBook pro', 'macBook air', 'macbook'],
                'Mac Accessories' => [],
            ],

            'iPhone, iPad, and iPod Touch' => [
                'iOS' => ['iOS12', 'iOS13', 'iOS14'],
                'iPhone' => ['iPhone', 'iPhone Accessories', 'iPhone tips'],
                'iPad' => ['iPad', 'iPad Accessories'],
                'iPod Touch' => [],
            ],

            'News and Article Discussion' => [
                'News Discussion' => [],
            ],

            'Apple Wearables' => [
                'AirPods' => [],
                'Apple Watch' => [],
                'Apple Watch Accessories' => [],
                'Apple Glasses' => [],
            ],
            'Software' => [
                'Mac Apps' => [],
                'Developers' => ['iOS', 'Mac', 'tvOS', 'watchOS'],
                'Console Games' => [],
                'Mac and PC Games' => [],
            ],
        ];

        foreach ($groups as $group => $categories) {

            $groupCategory = factory('App\GroupCategory')->create([
                'title' => $group,
            ]);

            foreach ($categories as $category => $subCategories) {

                $parentCategory = factory('App\Category')->create([
                    'title' => $category,
                    'group_category_id' => $groupCategory->id,
                ]);

                if (empty($subCategories)) {
                    $this->createThreadWithReplies($parentCategory);
                }
                foreach ($subCategories as $subCategory) {

                    $childrenCategory = factory('App\Category')->create([
                        'parent_id' => $parentCategory->id,
                        'group_category_id' => $groupCategory->id,
                        'title' => $subCategory,
                    ]);
                    $this->createThreadWithReplies($childrenCategory);
                }
            }
        }

    }

    public function createThreadWithReplies($category)
    {
        $thread = factory('App\Thread', static::NUM_OF_THREADS)->create([
            'category_id' => $category->id,
            'replies_count' => static::NUM_OF_REPLIES,
        ])->each(function ($currentThread) {
            factory('App\Reply', static::NUM_OF_REPLIES)->create([
                'repliable_id' => $currentThread->id,
                'repliable_type' => 'App\Thread',
            ]);

        });

    }
}