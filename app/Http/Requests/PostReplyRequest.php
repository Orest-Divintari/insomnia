<?php

namespace App\Http\Requests;

use App\Http\Middleware\MustBeVerified;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PostReplyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('add_reply', $this->route('thread'));
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function failedAuthorization()
    {
        throw new AuthorizationException(MustBeVerified::EXCEPTION_MESSAGE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body' => ['string', 'required'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'body.required' => 'Please enter a valid message.',
            'body.string' => 'Please enter a valid message.',
        ];
    }

}