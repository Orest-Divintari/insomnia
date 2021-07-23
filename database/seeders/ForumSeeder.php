<?php
namespace Database\Seeders;

use App\Models\Category;
use App\Models\GroupCategory;
use App\Models\Reply;
use App\Models\Thread;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    const NUM_OF_REPLIES = 5;
    const NUM_OF_THREADS = 2;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        config(['scout.driver' => null]);

        $groups = [
            'Macs' => [
                'macOS' => ['macOS catalina'],
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

            $groupCategory = GroupCategory::factory()->create([
                'title' => $group,
            ]);

            foreach ($categories as $category => $subCategories) {

                $parentCategory = Category::factory()->create([
                    'title' => $category,
                    'group_category_id' => $groupCategory->id,
                ]);

                if (empty($subCategories)) {
                    $this->createThreadWithReplies($parentCategory);
                } else {
                    foreach ($subCategories as $subCategory) {

                        $childrenCategory = Category::factory()->create([
                            'parent_id' => $parentCategory->id,
                            'group_category_id' => $groupCategory->id,
                            'title' => $subCategory,
                        ]);
                        $this->createThreadWithReplies($childrenCategory);
                    }
                }
            }
        }

    }

    public function createThreadWithReplies($category)
    {
        for ($threadCounter = 0; $threadCounter < static::NUM_OF_THREADS; $threadCounter++) {
            $thread = Thread::factory()->create([
                'category_id' => $category->id,
                'replies_count' => 0,
            ]);

            for ($replyCounter = 0; $replyCounter < static::NUM_OF_REPLIES; $replyCounter++) {
                Reply::factory()->create([
                    'repliable_id' => $thread->id,
                    'repliable_type' => 'App\Models\Thread',
                ]);
            }

        }
    }
}