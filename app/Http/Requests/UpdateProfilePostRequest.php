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
            'body' => 'required',
        ];
    }

    /**
     * Update the profile post
     *
     * @param  User $profileUser
     * @param  ProfilePost $post
     * @return ProfilePost
     */
    public function update($post)
    {
        return $post->update([
            'body' => request('body'),
        ]);
    }
}