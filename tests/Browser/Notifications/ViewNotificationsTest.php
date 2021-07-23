<?php

namespace Tests\Browser\Notifications;

use App\Events\Profile\NewPostWasAddedToProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use \Facades\Tests\Setup\ProfilePostFactory;

class ViewNotificationsTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function users_can_view_the_notifications()
    {
        $orestis = create(User::class);
        $john = create(User::class);
        $post = ProfilePostFactory::by($john)->toProfile($orestis)->create();
        event(new NewPostWasAddedToProfile($post, $john, $orestis));

        $this->browse(function (Browser $browser) use ($orestis, $post, $john) {
            $browser->loginAs($orestis)
                ->visit(route('account.notifications.index'))
                ->assertSee($john->name)
                ->assertSee($post->date_created);
        });
    }
}
