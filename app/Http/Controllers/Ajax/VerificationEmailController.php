<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;

class VerificationEmailController extends Controller
{

    /**
     * Send a verification email
     *
     * @return Illuminate\Http\Response
     */
    public function store()
    {
        if (auth()->user()->hasVerifiedEmail()) {
            abort(403, 'Your email has already been verified');
        }

        request()->validate([
            'g-recaptcha-response' => ['required', app(Recaptcha::class)],
        ]);

        auth()->user()->sendEmailVerificationNotification();

        return response('The verification email has been resent', 200);
    }
}