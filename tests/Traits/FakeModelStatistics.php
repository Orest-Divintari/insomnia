<?php

namespace Tests\Traits;

use App\Statistics\ThreadReplyStatistics;
use App\Statistics\ThreadStatistics;
use App\Statistics\UserStatistics;
use Tests\Unit\Statistics\FakeThreadReplyStatistics;
use Tests\Unit\Statistics\FakeThreadStatistics;
use Tests\Unit\Statistics\FakeUserStatistics;

trait FakeModelStatistics
{
    public function fakeStatistics()
    {
        app()->instance(ThreadStatistics::class, new FakeThreadStatistics);
        app()->instance(ThreadReplyStatistics::class, new FakeThreadReplyStatistics);
        app()->instance(UserStatistics::class, new FakeUserStatistics);
    }
}