<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LikeSeeder extends Seeder

    use RandomModels;

    const NUMBER_OF_THREAD_REPLIES = 2;
    const NUMBER_OF_COMMENTS = 2;
    const NUMBER_OF_PROFILEPOSTS = 2;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->randomUsers(1000)->each(function ($user) {
            $this->likeProfilePosts($user);
            $this->likeComments($user);
            $this->likeThreadReplies($user);
        });
    }

    protected function likeComments($user)
    {
        $comments = $this->randomComments(static::NUMBER_OF_COMMENTS);
        $comments->each(function ($comment) use ($user) {
            $comment->like($user);
        });
    }

    protected function likeProfilePosts($user)
    {
        $posts = $this->randomProfilePosts(static::NUMBER_OF_PROFILEPOSTS);

        $posts->each(function ($post) use ($user) {
            $post->like($user);
        });
    }

    protected function likeThreadReplies($user)
    {
        $threadReplies = $this->randomThreadReplies(static::NUMBER_OF_THREAD_REPLIES);

        $threadReplies->each(function ($reply) use ($user) {
            $reply->like($user);
        });
    }
}