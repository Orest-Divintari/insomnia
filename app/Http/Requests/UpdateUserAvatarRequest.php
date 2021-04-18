<?php

namespace App\Http\Requests;

use App\Facades\Avatar;
use App\Rules\GravatarExists;
use App\User;
use Creativeorange\Gravatar\Facades\Gravatar;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserAvatarRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'avatar' => ['sometimes', 'required', 'image', 'mimes:jpeg,png,jpg', 'max:10000'],
            'gravatar' => ['sometimes', 'required', 'email', new GravatarExists],
        ];
    }

    public function persist()
    {
        auth()->user()->update([
            'avatar_path' => $this->getAvatarPath(),
            'default_avatar' => false,
            'gravatar' => $this->getGravatar(),
        ]);

    }

    protected function getAvatarPath()
    {
        $username = auth()->user()->name;

        if ($this->has('avatar')) {

            $this->deleteExistingAvatar($username);

            return $this->persistAvatar($username);

        } elseif ($this->has('gravatar')) {
            return Gravatar::get($this->input('gravatar'));
        }
    }

    public function getGravatar()
    {
        return $this->has('gravatar') ? $this->input('gravatar') : $this->user()->gravatar;
    }

    public function messages()
    {
        return [
            'gravatar.required' => 'Gravatars require valid email addresses.',
            'gravatar.email' => 'Gravatars require valid email addresses.',
        ];
    }

    protected function deleteExistingAvatar($username)
    {
        Avatar::delete($username);
    }

    protected function persistAvatar($username)
    {
        return $this->file('avatar')
            ->store("/images/avatars/users/{$username}");
    }
}