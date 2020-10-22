<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FollowRequest;
use App\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{

    protected $request;
    protected $followRequest;
    /**
     * Create a new instance
     *
     * @param Request $request
     */
    public function __construct(Request $request, FollowRequest $followRequest)
    {
        $this->request = $request;
        $this->followRequest = $followRequest;
    }

    /**
     * Store a new follow
     *
     * @param User $user
     * @return void
     */
    public function store()
    {
        $this->request->user()
            ->follow($this->followRequest->getUser());
    }

    /**
     * Remove an existing follow
     *
     * @return void
     */
    public function destroy()
    {
        $this->request->user()
            ->unfollow($this->followRequest->getUser());
    }

}