<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserEmailRequest;

class UserEmailController extends Controller
{
    public function update(UpdateUserEmailRequest $request)
    {
        $request->persist();

        return response('Your email has been updated', 200);
    }
}