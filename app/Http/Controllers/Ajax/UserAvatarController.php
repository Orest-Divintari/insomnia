<?php

namespace App\Http\Controllers\Ajax;

use App\Facades\Avatar;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserAvatarRequest;
use App\User;

class UserAvatarController extends Controller
{

    /**
     * Update the avatar of the user
     *
     * @param UpdateUserAvatarRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserAvatarRequest $request)
    {
        $request->persist();

        return auth()->user()->fresh();
    }

    /**
     * Remove the current avatar of the user
     *
     * @return \Illuminate\Http\Response
     */
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