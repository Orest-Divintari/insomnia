<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $this->prepareForValidation();

        $request->validate([
            $this->username() => "required|string",
            'password' => 'required|string',
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return request()->filled('email') ? 'email' : 'name';
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // the input element has name 'email'
        // therefore i use the 'email' key to get the value
        $identityValue = request('email');
        $identityKey = str_contains($identityValue, '@') ? 'email' : 'name';
        $identityKeyToBeRemoved = '';

        if ($identityKey == 'name') {
            $$identityKeyToBeRemoved = 'email';
        } else {
            $$identityKeyToBeRemoved = 'name';
        }

        request()->merge([
            $identityKey => $identityValue,
            $$identityKeyToBeRemoved => null,
        ]);
    }
}