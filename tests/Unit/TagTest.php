<?php

namespace Tests\Unit;

use App\Tag;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_tag_may_have_threads()
    {
        $tag = create(Tag::class);
        $this->assertCount(0, $tag->threads);
        $thread = create(Thread::class);

        $thread->addTags([$tag->name]);

        $tag->refresh();
        $this->assertCount(1, $tag->threads);
        $this->assertEquals($thread->id, $tag->threads->first()->id);
    }
}