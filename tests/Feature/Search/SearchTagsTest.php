<?php

namespace Tests\Feature\Search;

use App\Models\Tag;
use App\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTagsTest extends SearchTest
{
    use RefreshDatabase;

    /** @test */
    public function when_a_user_searches_a_single_tag_all_threads_related_to_the_tag_are_displayed()
    {
        $this->withoutExceptionHandling();
        $tagApple = create(Tag::class, ['name' => 'apple']);
        $numberOfDesiredThreads = 2;
        $threads = createMany(Thread::class, $numberOfDesiredThreads);
        foreach ($threads as $thread) {
            $thread->addTags([$tagApple->name]);
        }
        $this->assertCount(2, $tagApple->threads);

        $results = $this->search(
            ['type' => 'tag', 'q' => $tagApple->name],
            $numberOfDesiredThreads
        );

        $this->assertCount($numberOfDesiredThreads, $results);
    }

    /** @test */
    public function when_a_user_searches_multiple_tags_all_threads_related_to_all_given_tags_are_displayed()
    {
        $this->withoutExceptionHandling();
        $tagApple = create(Tag::class, ['name' => 'applelicious']);
        $tagMicrosoft = create(Tag::class, ['name' => 'microsoftbil']);
        $appleThreads = 2;
        $microsoftThreads = 4;
        $numberOfDesiredThreads = 1;
        $threads = createMany(Thread::class, $appleThreads);
        foreach ($threads as $thread) {
            $thread->addTags([$tagApple->name]);
        }
        $threads->first()->addTags([$tagMicrosoft->name]);

        $threads = createMany(Thread::class, $microsoftThreads);
        foreach ($threads as $thread) {
            $thread->addTags([$tagMicrosoft->name]);
        }

        $tags = "{$tagApple->name},{$tagMicrosoft->name}";
        $results = $this->search(
            ['type' => 'tag', 'q' => $tags],
            $numberOfDesiredThreads
        );

        $this->assertCount($numberOfDesiredThreads, $results);
    }

    /** @test */
    public function when_users_search_for_tags_that_dont_exist_in_the_database_then_an_error_message_is_displayed()
    {
        $nonExistingTag = 'randomTag';

        $response = $this->get(route(
            'search.index',
            ['type' => 'tag', 'q' => $nonExistingTag]
        ),
        );

        $response->assertSee('The following tag could not be found: ' . $nonExistingTag);
    }
}