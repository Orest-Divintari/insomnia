<?php

namespace Tests\Feature;

use App\Check;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use App\Search\SearchData;
use Carbon\Carbon;
use ElasticScoutDriverPlus\Builders\RangeQueryBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use \Facades\Tests\Setup\ReplyFactory;

class ElasticTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function search()
    {

        $models = [];

        $models['threads'][] = 1;
        $models['threads'][] = 2;
        $this->withoutExceptionHandling();
        config(['scout.driver' => 'elastic']);
        $user = create(User::class, ['name' => 'orestis']);
        create(Thread::class, ['title' => 'ena arxidi sto gialo hello malaka moynopano', 'user_id' => $user->id]);
        create(Thread::class, ['title' => 'hello', 'user_id' => $user->id]);
        create(Thread::class, ['title' => 'hello']);
        sleep(20);
        $threads = Thread::boolSearch()
            ->should('wildcard', ['title' => '*hello*'])
            ->filter('terms', ['user_id' => [1, 2]])
            ->execute()
            ->models()
            ->count();
        ReplyFactory::create(['body' => 'hello']);

        dd(Thread::where('title', 'hello')->first());
        // dd($a, 'qq');
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

        // dd(Thread::search('yo')->get());
        // dd(Thread::boolSearch()->should('match', ['title' => 'yo'])->execute()->models());
        // dd(Thread::all()->pluck('created_at'));

        // Execute your query here
        $count = 0;
        DB::listen(function ($query) use (&$count) {
            $count++;
        });
        $threads = Thread::boolSearch()
            ->join(Reply::class)
            ->should('wildcard', ['title' => '*'])
            ->paginate(10);

        dd($threads->matches()->first()->document()->getContent()['id']);
        $matches = [];
        foreach ($threads->matches() as $match) {
            array_push($matches, $match);
        }
        dd($matches);
        dd($threads->matches()->last()->model(), $count);
        dd($threads->matches->first()->model());
        dd($threads, 'bb');

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