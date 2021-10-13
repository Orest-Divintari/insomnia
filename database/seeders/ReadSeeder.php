<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Thread;
use Illuminate\Database\Seeder;

class ReadSeeder extends Seeder
{
    use RandomModels;

    const NUMBER_OF_USERS = 1;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Conversation::all()->each(function ($conversation) {
            $conversation->participants->each(function ($participant) use ($conversation) {
                $conversation->read($participant);
            });
        });

        $users = $this->randomUsers(static::NUMBER_OF_USERS);
        Thread::all()->each(function ($thread) use ($users) {
            $users->each(function ($user) use ($thread) {
                $thread->read($user);
            });
        });
    }
}