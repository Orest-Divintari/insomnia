<?php

namespace App\Http\Controllers\Ajax;

use App\Facades\Avatar;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserAvatarRequest;
use App\User;

class UserAvatarController extends Controller
{

    public function update(UpdateUserAvatarRequest $request)
    {
        $request->persist();

        return auth()->user()->fresh();
    }

    public function destroy()
    {
        $user = auth()->user();

        $user->update([
            'avatar_path' => null,
            'default_avatar' => true,
        ]);

        Avatar::delete($user->name);

        return $user->fresh();
    }
}