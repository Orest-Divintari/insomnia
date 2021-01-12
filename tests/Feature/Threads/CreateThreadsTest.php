<?php

namespace Tests\Feature\Threads;

use App\Category;
use App\Http\Middleware\ThrottlePosts;
use App\Tag;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $bodyErrorMessage;
    protected $titleErrorMessage;
    protected $categoryErrorMessage;
    protected $tagErrorMessage;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ThrottlePosts::class]);

        $this->bodyErrorMessage = 'Please enter a valid message.';
        $this->titleErrorMessage = 'Please enter a valid title.';
        $this->categoryErrorMessage = 'Please enter a valid category.';
        $this->tagErrorMessage = "The following tag could not be found: ";
    }

    /** @test */
    public function guests_may_not_see_the_post_new_thread_form()
    {
        $category = create(Category::class);

        $response = $this->get(route('threads.create', $category));

        $response->assertRedirect('login');
    }

    /** @test */
    public function guests_may_not_post_new_threads()
    {
        $response = $this->post(route('threads.store'), []);

        $response->assertRedirect('login');
    }

    /** @test */
    public function an_authenticated_user_has_to_verify_the_email_before_posting_a_new_thread()
    {
        $user = create(User::class, [
            'email_verified_at' => null,
        ]);
        $this->signIn($user);

        $response = $this->post(route('threads.store'), []);

        $response->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function authenticated_users_that_have_confirmed_their_email_may_postThreads()
    {
        $this->signIn();
        $thread = raw(Thread::class);
        $title = ['title' => $thread['title']];

        $this->post(route('threads.store'), $thread);

        $this->assertDatabaseHas('threads', $title);

    }

    /** @test */
    public function a_user_that_has_exceeded_the_post_rate_limit_cannot_create_a_thread()
    {
        $this->withMiddleware([ThrottlePosts::class]);
        $this->signIn();
        $error = "post_throttled";

        $this->post(
            route('threads.store'),
            raw(Thread::class)
        );
        $response = $this->post(
            route('threads.store'),
            raw(Thread::class)
        );

        $response->assertSessionHasErrors($error);
    }

    /** @test */
    public function a_reply_is_created_when_a_new_thread_is_created_as_the_body_of_the_thread()
    {
        $user = $this->signIn();
        $thread = raw(Thread::class, [
            'user_id' => $user->id,
        ]);

        $this->post(route('threads.store', $thread));

        $this->assertDatabaseHas('replies', [
            'body' => $thread['body'],
            'user_id' => $thread['user_id'],
            'position' => 1,
        ]);
    }

    /** @test */
    public function a_thread_requires_a_body()
    {
        $response = $this->postThread(['body' => '']);

        $response->assertSessionHasErrors(['body' => $this->bodyErrorMessage]);
    }

    /** @test */
    public function the_body_of_a_thread_must_be_of_stype_string()
    {
        $response = $this->postThread(['body' => 15]);

        $response->assertSessionHasErrors(['body' => $this->bodyErrorMessage]);
    }

    /** @test */
    public function a_thread_requires_a_title()
    {
        $response = $this->postThread(['title' => '']);

        $response->assertSessionHasErrors(['title' => $this->titleErrorMessage]);
    }

    /** @test */
    public function a_thread_title_must_be_of_type_string()
    {
        $response = $this->postThread(['title' => 15]);

        $response->assertSessionHasErrors(['title' => $this->titleErrorMessage]);
    }

    /** @test */
    public function a_thread_requires_a_category()
    {
        $response = $this->postThread(['category_id' => '']);

        $response->assertSessionHasErrors(['category_id' => $this->categoryErrorMessage]);
    }

    /** @test */
    public function a_thread_requires_a_category_that_already_exists_in_the_database()
    {
        $response = $this->postThread(['category_id' => 12345]);

        $response->assertSessionHasErrors(['category_id' => $this->categoryErrorMessage]);
    }

    /** @test */
    public function the_category_value_must_be_of_type_integer()
    {
        $response = $this->postThread(['category_id' => '12345']);

        $response->assertSessionHasErrors(['category_id' => $this->categoryErrorMessage]);
    }

    /** @test */
    public function authenticated_users_may_add_a_single_tag_when_creating_a_thread()
    {
        $this->signIn();
        $tag = create(Tag::class);
        $thread = raw(Thread::class);

        $this->post(
            route(
                'threads.store',
                array_merge($thread, ['tags' => $tag->name])
            )
        );

        $thread = Thread::whereSlug($thread['slug'])->first();
        $this->assertCount(1, $thread->tags);
        $this->assertEquals($tag->id, $thread->tags->first()->id);
    }

    /** @test */
    public function authenticated_users_may_add_multiple_tags_when_creating_a_thread()
    {
        $this->signIn();
        $tagApple = create(Tag::class, ['name' => 'apple']);
        $tagSamsung = create(Tag::class, ['name' => 'samsung']);
        $tags = "{$tagApple->name}, {$tagSamsung->name}";
        $thread = raw(Thread::class);

        $this->post(
            route(
                'threads.store',
                array_merge($thread, ['tags' => $tags])
            )
        );

        $thread = Thread::whereSlug($thread['slug'])->first();
        $this->assertCount(2, $thread->tags);
        $this->assertContains($tagApple->id, $thread->tags->pluck('id'));
        $this->assertContains($tagSamsung->id, $thread->tags->pluck('id'));
    }

    /** @test */
    public function the_tag_that_is_added_to_a_thread_must_already_exist_in_the_database()
    {
        $this->signIn();
        $nonExistingTag = 'randomTag';
        $thread = raw(Thread::class);

        $response = $this->post(
            route(
                'threads.store',
                array_merge($thread, ['tags' => $nonExistingTag]))
        );

        $response->assertSessionHasErrors([
            'tags.0' => $this->tagErrorMessage . $nonExistingTag],
        );
    }

    /** @test */
    public function when_multiple_tags_are_added_to_a_thread_they_must_exist_in_the_database()
    {
        $this->signIn();
        $nonExistingTags = 'randomTag, randomTag2';
        $thread = raw(Thread::class);

        $response = $this->post(
            route(
                'threads.store',
                array_merge($thread, ['tags' => $nonExistingTags]))
        );

        $response->assertSessionHasErrors([
            'tags.0' => $this->tagErrorMessage . 'randomTag',
            'tags.1' => $this->tagErrorMessage . 'randomTag2',
        ]);
    }

    /** @test */
    public function multiple_tags_can_be_entered_as_an_array()
    {
        $this->signIn();
        $appleTag = create(Tag::class);
        $samsungTag = create(Tag::class);
        $arrayTags = [$appleTag->name, $samsungTag->name];
        $thread = raw(Thread::class);

        $this->post(
            route(
                'threads.store',
                array_merge($thread, ['tags' => $arrayTags]))
        );

        $thread = Thread::whereSlug($thread['slug'])->first();
        $this->assertCount(2, $thread->tags);
    }

    /** @test */
    public function multiple_tags_can_be_entered_as_comma_separated_string_which_is_then_converted_to_an_array()
    {
        $this->signIn();
        $appleTag = create(Tag::class);
        $samsungTag = create(Tag::class);
        $commaSeparatedStringTags = $appleTag->name . ',' . $samsungTag->name;
        $thread = raw(Thread::class);

        $this->post(
            route(
                'threads.store',
                array_merge($thread, ['tags' => $commaSeparatedStringTags]))
        );

        $thread = Thread::whereSlug($thread['slug'])->first();
        $this->assertCount(2, $thread->tags);
    }

    /** @test */
    public function each_tag_must_be_of_type_string()
    {
        $this->signIn();
        $nonStringTag = 5;
        $thread = raw(Thread::class);

        $response = $this->post(
            route(
                'threads.store',
                array_merge($thread, ['tags' => $nonStringTag]))
        );

        $response->assertSessionHasErrors('tags.0');
    }

    protected function postThread($overrides)
    {
        $user = $this->signIn();

        $thread = raw(Thread::class, $overrides + ['user_id' => $user->id]);

        return $this->post(route('threads.store'), $thread);
    }

    /** @test */
    public function a_thread_requires_a_unique_slug()
    {
        $user = $this->signIn();
        $this->assertUniqueSlug('some title', 'some-title');
        $this->assertUniqueSlug('some title', 'some-title.2');
        $this->assertUniqueSlug('some title', 'some-title.3');
        $this->assertUniqueSlug('some title 55', 'some-title-55');
        $this->assertUniqueSlug('some title 55', 'some-title-55.2');
    }

    public function assertUniqueSlug($title, $slug)
    {
        $thread = raw(Thread::class, [
            'title' => $title,
        ]);

        $this->post(route('threads.store'), $thread);

        $this->assertEquals(Thread::latest('id')->first()->slug, $slug);

    }
}