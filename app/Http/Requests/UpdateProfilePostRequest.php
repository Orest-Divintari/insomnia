<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $post = $this->route('post');
        return $post && $this->user()->can('manage', $post);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body' => ['required', 'string'],
        ];
    }

    /**
     * Update the profile post
     *
     * @param  User $profileOwner
     * @param  ProfilePost $post
     * @return ProfilePost
     */
    public function update($post)
    {
        $post->update(['body' => request('body')]);

        return $post;
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