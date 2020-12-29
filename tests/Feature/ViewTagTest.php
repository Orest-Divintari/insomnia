<?php

namespace Tests\Feature;

use App\Tag;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewTagTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function display_the_threads_that_are_correlated_with_a_tag()
    {
        $this->withoutExceptionHandling();
        $threads = createMany(Thread::class, 2);
        $tag = create(Tag::class);
        foreach ($threads as $thread) {
            $thread->addTags([$tag->name]);
        }
        $this->assertCount(2, $tag->fresh()->threads);
        $threads = $threads->toArray();

        $response = $this->get(route('tags.show', $tag));

        $response->assertSee($threads[0]['title'])
            ->assertSee($threads[1]['title']);

    }
}