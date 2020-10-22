<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{

    protected $request;

    /**
     * Create a new instance
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Toggle follow
     *
     * @param User $user
     * @return void
     */
    public function store()
    {
        $user = $this->request->validate([
            'username' => 'exists:users,name',
        ]);

        $user = User::whereName($user['username'])->first();

        $this->request->user()->toggleFollow($user);
    }
}