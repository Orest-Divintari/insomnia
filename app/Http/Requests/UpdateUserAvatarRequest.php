<?php

namespace App\Http\Requests;

use App\Facades\Avatar;
use App\Models\User;
use App\Rules\GravatarExists;
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
            'gravatar_email' => ['sometimes', 'required', 'email', new GravatarExists],
        ];
    }

    public function messages()
    {
        return [
            'gravatar_email.required' => 'Gravatars require valid email addresses.',
            'gravatar_email.email' => 'Gravatars require valid email addresses.',
        ];
    }

    public function persist()
    {
        if ($this->has('avatar')) {
            $this->persistAvatar($this->user()->name);
        } elseif ($this->hasGravatar()) {
            $this->persistGravatar();
        }
    }

    protected function persistAvatar($username)
    {
        $this->deleteExistingAvatar($username);

        $this->user()->update([
            'avatar_path' => $this->getAvatarPath($username),
            'default_avatar' => false,
        ]);
    }

    protected function persistGravatar()
    {
        $gravatarPath = $this->getGravatarPath();

        $this->user()->update([
            'avatar_path' => $gravatarPath,
            'gravatar_path' => $gravatarPath,
            'gravatar_email' => $this->input('gravatar_email'),
            'default_avatar' => false,
        ]);
    }

    protected function hasGravatar()
    {
        return $this->isNewGravatar($this->input('gravatar_email'));
    }

    protected function getAvatarPath($username)
    {
        return $this->storeAvatarFile($username);
    }

    protected function getGravatarPath()
    {
        return Gravatar::get($this->input('gravatar_email'), ['size' => 400]);
    }

    protected function isNewGravatar($gravatarEmail)
    {
        return $gravatarEmail != $this->user()->gravatar_mail;
    }

    protected function deleteExistingAvatar($username)
    {
        Avatar::delete($username);
    }

    protected function storeAvatarFile($username)
    {
        return $this->file('avatar')
            ->store("/images/avatars/users/{$username}");
    }
}
