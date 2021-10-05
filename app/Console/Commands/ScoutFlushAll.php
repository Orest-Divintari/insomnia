<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ScoutFlushAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scout:flushall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all records from all search indices';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Artisan::call("scout:flush 'App\\\Models\\\Thread'");
        Artisan::call("scout:flush 'App\\\Models\\\Reply'");
        Artisan::call("scout:flush 'App\\\Models\\\User'");
        Artisan::call("scout:flush 'App\\\Models\\\ProfilePost'");
        Artisan::call("scout:flush 'App\\\Models\\\Tag'");
    }
}