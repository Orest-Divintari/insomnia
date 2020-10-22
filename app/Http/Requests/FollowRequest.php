<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;

class FollowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'exists:users,name',
        ];
    }

    /**
     * Get the requested user
     *
     * @return User
     */
    public function getUser()
    {
        return User::whereName(
            $this->input('username')
        )->first();
    }

}