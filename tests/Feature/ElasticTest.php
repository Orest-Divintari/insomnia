<?php

namespace Tests\Feature;

use App\Check;
use App\Models\Thread;
use App\Models\User;
use App\Search\SearchData;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ElasticTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function search()
    {
        $this->withoutExceptionHandling();
        config(['scout.driver' => 'elastic']);
        $user = create(User::class);
        create(Thread::class, ['title' => 'hello', 'user_id' => $user->id]);
        create(Thread::class, ['title' => 'hello']);
        ReplyFactory::create(['body' => 'hello', 'user_id' => $user->id]);

        dd(Thread::search('hello')->where('id', '>', 1));
    }

    /** @test */
    public function check()
    {
        $s = new SearchData(['type' => 'a', 'onlyTitle' => true, 'query' => 'asdf', 'ela' => 're']);
        dd($s->ela);
    }
}