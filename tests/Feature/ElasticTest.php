<?php

namespace Tests\Feature;

use App\Check;
use App\Models\Thread;
use App\Models\User;
use App\Search\SearchData;
use Carbon\Carbon;
use ElasticScoutDriverPlus\Builders\RangeQueryBuilder;
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
        $user = create(User::class, ['name' => 'orestis']);
        // create(Thread::class, ['title' => 'ena arxidi sto gialo hello malaka moynopano', 'user_id' => $user->id]);
        // create(Thread::class, ['title' => 'hello', 'user_id' => $user->id]);
        // ReplyFactory::create(['body' => 'hello']);

        // // dd(Thread::search('*')->get());
        // dd(Thread::boolSearch()
        //         ->join(Reply::class)
        //         ->should('wildcard', ['title' => '*hell*'])
        //         ->should('wildcard', ['body' => '*hell*'])
        //         ->filter('term', ['user_id' => $user->id])
        //         ->execute()
        //         ->models()
        // );

        Carbon::setTestNow(Carbon::now()->subMonth());
        create(Thread::class, ['title' => 'yo', 'user_id' => $user->id]);
        Carbon::setTestNow();
        create(Thread::class, ['title' => 'arxidi']);

        // dd(Thread::all()->pluck('created_at'));

        dd(Thread::boolSearch()
                ->should('wildcard', ['title' => '*'])
                ->filter((new RangeQueryBuilder())->field('created_at')->gte(Carbon::now()->subMonth()->startOfDay()))
                ->filter('term', ['user_id' => $user->id])
                ->execute()
                ->models());

        // ->query('hello')->paginate()->models()

    }

    /** @test */
    public function check()
    {
        $s = new SearchData(['type' => 'a', 'onlyTitle' => true, 'query' => 'asdf', 'ela' => 're']);
        dd($s->ela);
    }
}