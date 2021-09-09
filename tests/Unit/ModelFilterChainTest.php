<?php

namespace Tests\Unit;

use App\Filters\ConversationFilters;
use App\Filters\ElasticProfilePostFilters;
use App\Filters\ElasticThreadFilters;
use App\Filters\FilterChain;
use App\Filters\ProfilePostFilters;
use App\Filters\ReplyFilters;
use App\Filters\ThreadFilters;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelFilterChainTest extends TestCase
{
    use RefreshDatabase;

    protected $chain;
    public function setUp(): void
    {
        parent::setUp();
        $this->chain = new FilterChain();
    }

    /** @test */
    public function a_chain_may_have_thread_filters()
    {
        $this->chain->withThreadFilters();

        $this->assertContains(ThreadFilters::class, $this->chain->getFilters());
    }

    /** @test */
    public function a_chain_may_have_profile_post_filters()
    {
        $this->chain->withProfilePostFilters();

        $this->assertContains(ProfilePostFilters::class, $this->chain->getFilters());
    }

    /** @test */
    public function a_chain_may_have_reply_filters()
    {
        $this->chain->withReplyFilters();

        $this->assertContains(ReplyFilters::class, $this->chain->getFilters());
    }

    /** @test */
    public function a_chain_may_have_all_posts_filters()
    {
        $this->chain->withAllPostsFilters();

        $this->assertContains(ThreadFilters::class, $this->chain->getFilters());
        $this->assertContains(ProfilePostFilters::class, $this->chain->getFilters());
    }

    /** @test */
    public function a_chain_may_have_conversation_filters()
    {
        $this->chain->withConversationFilters();

        $this->assertContains(ConversationFilters::class, $this->chain->getFilters());
    }

    /** @test */
    public function a_chain_may_have_elastic_thread_filters()
    {
        $this->chain->withElasticThreadFilters();

        $this->assertContains(ElasticThreadFilters::class, $this->chain->getFilters());
    }

    /** @test */
    public function a_chain_may_have_elastic_profile_post_filters()
    {
        $this->chain->withElasticProfilePostFilters();

        $this->assertContains(ElasticProfilePostFilters::class, $this->chain->getFilters());
    }

    /** @test */
    public function a_chain_may_have_elastic_all_posts_filters()
    {
        $this->chain->withElasticAllPostsFilters();

        $this->assertContains(ElasticThreadFilters::class, $this->chain->getFilters());
        $this->assertContains(ElasticProfilePostFilters::class, $this->chain->getFilters());
    }

    /** @test */
    public function a_chain_can_add_a_new_filter()
    {
        $this->chain->addFilter(ThreadFilters::class);

        $this->assertCount(1, $this->chain->getFilters());
    }

    /** @test */
    public function a_chain_can_return_the_stored_filters()
    {
        $this->chain->addFilter(ThreadFilters::class);

        $this->assertCount(1, $this->chain->getFilters());
    }

}