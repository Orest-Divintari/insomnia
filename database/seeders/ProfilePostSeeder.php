<?php

namespace Database\Seeders;

use App\Models\ProfilePost;
use App\Models\Reply;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Database\Seeder;

class ProfilePostSeeder extends Seeder
{

    const NUMBER_OF_PROFILE_POSTS = 2;
    const NUMBER_OF_COMMENTS = 2;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $posters = $this->randomUsers(2000);

        $posters->each(function ($poster) {

            $this->signIn($poster);
            $profileOwner = $this->randomUser();

            $profilePosts = ProfilePostFactory::by($poster)
                ->toProfile($profileOwner)
                ->createMany(static::NUMBER_OF_PROFILE_POSTS);

            $profilePosts->each(function ($profilePost) use ($poster) {
                Reply::factory()->create([
                    'repliable_id' => $profilePost->id,
                    'repliable_type' => ProfilePost::class,
                    'user_id' => $poster->id,
                ]);

            });
        });
    }
}