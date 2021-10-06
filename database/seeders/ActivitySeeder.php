<?php

namespace Database\Seeders;

use App\Actions\LogOnlineUserActivityAction;
use App\Events\Activity\UserViewedPage;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{

    use AuthenticatesUsers, RandomModels;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = $this->randomUsers(2000);

        $users->each(function ($user) {
            $this->signIn($user);

            $logger = app(LogOnlineUserActivityAction::class);

            $logger->execute(UserViewedPage::FORUM);

            $logger->execute(UserViewedPage::CONVERSATION);
        });
    }

}