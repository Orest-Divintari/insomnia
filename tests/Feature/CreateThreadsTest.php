<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_create_threads()
    {
        $this->withExceptionHandling();
        $this->post('/api/threads', [])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}