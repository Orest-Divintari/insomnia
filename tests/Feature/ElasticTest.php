<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Thread;
use Carbon\Carbon;
use ElasticScoutDriverPlus\Builders\RangeQueryBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use \Facades\Tests\Setup\ReplyFactory;

class ElasticTest extends TestCase
{
    use RefreshDatabase;

    public function search()
    {
        config(['scout.driver' => 'elastic']);

        create(Thread::class, ['title' => 'hello']);
        create(Thread::class, ['title' => 'HELLO']);
        create(Thread::class, ['title' => 'hElL0']);
        create(Thread::class, ['title' => 'elafilehellofilemou']);

        $threads = Thread::boolSearch()
            ->filter('wildcard', ['title' => '*hel*'])
            ->execute();

        dd($threads);
        // $thread1 = create(Thread::class, ['title' => 'hi', 'body' => 'bye']);
        // $thread2 = create(Thread::class, ['body' => 'bye']);
        // $thread3 = create(Thread::class, ['title' => 'hi']);
        // $apple = create(Tag::class, ['name' => 'apple']);
        // $ms = create(Tag::class, ['name' => 'microsoft']);
        // $thread1->addTags([$apple->name, $ms->name]);
        // $thread2->addTags([$apple->name]);
        // $thread3->addTags([$ms->name]);

        // $results = Thread::boolSearch()
        //     ->filter('term', ['title' => 'hi'])
        //     ->filter('term', ['body' => 'bye'])
        //     ->execute();

        // dd($results);

        // $models = [];

        // $models['threads'][] = 1;
        // $models['threads'][] = 2;

        // $a = [];
        // $a['tags'][] = 1;
        // // $a['tags'][] = 2;
        // $this->withoutExceptionHandling();
        // config(['scout.driver' => 'elastic']);
        // $user = create(User::class, ['name' => 'orestis']);
        // $apple = Tag::create(['name' => 'apple']);
        // $thread = create(Thread::class, ['title' => 'ena arxidi sto gialo hello malaka moynopano', 'user_id' => $user->id]);
        // $thread->addTags([$apple->name]);
        // dd($thread->tags, 'qq');
        // $results = Thread::boolSearch()
        //     ->should('terms', ['tags' => [$apple->name]])
        //     ->execute();
        // dd($results);

        $results = Thread::boolSearch()
            ->should('match', ['title' => 'hello'])
            ->execute();

        dd($results);
        dd(Thread::search('apple')->get()->toArray());

        create(Thread::class, ['title' => 'hello', 'user_id' => $user->id]);

        create(Thread::class, ['title' => 'hello']);

        $thread = Thread::boolSearch()
            ->should('match', ['ena' => 1])
            ->execute()
            ->models()
            ->toArray();

        dd($thread);
        $results = Thread::nestedSearch()
            ->path('tags')
            ->query('match', ['tags.name' => 'apple'])
            ->execute()
            ->models()
            ->toArray();

        // dd($results);

        // dd(Thread::boolSearch()
        //         ->should('wildcard', ['title' => '*hello*']));

        $threads = Thread::boolSearch()
            ->should('wildcard', ['title' => '*hello*'])
            ->filter('terms', ['user_id' => [1, 2]])
            ->execute()
            ->models()
            ->count();
        ReplyFactory::create(['body' => 'hello']);

        dd(Reply::search('hello')->get()->toArray());

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
}