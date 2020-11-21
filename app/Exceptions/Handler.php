<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof TokenMismatchException && $request->getRequestUri() === '/logout') {

            Auth::logout();

            return redirect('/forum');
        }

        if ($exception instanceof ValidationException) {
            // $errorMessages = (new ValidationErrorMessage($exception))
            //     ->getMessages();

            // if ($request->expectsJson()) {
            //     return response($errorMessages, 422);
            // }

            // return redirect($exception->redirectTo ?? url()->previous())
            //     ->withInput(Arr::except($request->input(), $this->dontFlash))
            //     ->withErrors($exception->errors(), $exception->errorBag);

        }

        return parent::render($request, $exception);
    }
}