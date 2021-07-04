<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserEmailRequest;

class UserEmailController extends Controller
{
    /**
     * Update the email of the user
     *
     * @param UpdateUserEmailRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserEmailRequest $request)
    {
        $request->persist();

        return response('Your email has been updated', 200);
    }
}