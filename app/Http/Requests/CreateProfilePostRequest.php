<?php

namespace App\Http\Requests;

use App\ProfilePost;
use Illuminate\Foundation\Http\FormRequest;

class CreateProfilePostRequest extends FormRequest
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
            'body' => 'required|string',
        ];
    }

    public function persist($profileOwner)
    {
        return ProfilePost::create([
            'body' => $this->input('body'),
            'profile_owner_id' => $profileOwner->id,
            'poster_id' => $this->user()->id,
        ]);
    }
}
