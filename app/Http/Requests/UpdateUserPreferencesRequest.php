<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserPreferencesRequest extends FormRequest
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
            'thread_reply_created' => ['sometimes', 'required', 'array'],
            'thread_reply_created.*' => ['string', Rule::in('database')],
            'thread_reply_liked' => ['sometimes', 'required', 'array'],
            'thread_reply_liked.*' => ['string', Rule::in('database')],
            'profile_post_created' => ['sometimes', 'required', 'array'],
            'profile_post_created.*' => ['string', Rule::in('database')],
            'comment_on_a_post_on_your_profile_created' => ['sometimes', 'required', 'array'],
            'comment_on_a_post_on_your_profile_created.*' => ['string', Rule::in('database')],
            'comment_on_your_post_on_your_profile_created' => ['sometimes', 'required', 'array'],
            'comment_on_your_post_on_your_profile_created.*' => ['string', Rule::in('database')],
            'comment_on_your_profile_post_created' => ['sometimes', 'required', 'array'],
            'comment_on_your_profile_post_created.*' => ['string', Rule::in('database')],
            'comment_on_participated_profile_post_created' => ['sometimes', 'required', 'array'],
            'comment_on_participated_profile_post_created.*' => ['string', Rule::in('database')],
            'comment_liked' => ['sometimes', 'required', 'array'],
            'comment_liked.*' => ['string', Rule::in('database')],
            'message_liked' => ['sometimes', 'required', 'array'],
            'message_liked.*' => ['string', Rule::in('database')],
            'message_created' => ['sometimes', 'required', 'array'],
            'message_created.*' => ['string', Rule::in('mail')],
            'user_followed_you' => ['sometimes', 'required', 'array'],
            'user_followed_you.*' => ['string', Rule::in('database')],
            'subscribe_on_creation' => ['sometimes', 'required', 'accepted'],
            'subscribe_on_creation_with_email' => ['sometimes', 'required', 'accepted'],
            'subscribe_on_interaction' => ['sometimes', 'required', 'accepted'],
            'subscribe_on_interaction_with_email' => ['sometimes', 'required', 'accepted'],
            'mentioned_in_thread_reply' => ['sometimes', 'required', 'array'],
            'mentioned_in_thread_reply.*' => ['string', Rule::in('database')],
            'mentioned_in_thread' => ['sometimes', 'required', 'array'],
            'mentioned_in_thread.*' => ['string', Rule::in('database')],
            'mentioned_in_profile_post' => ['sometimes', 'required', 'array'],
            'mentioned_in_profile_post.*' => ['string', Rule::in('database')],
            'mentioned_in_comment' => ['sometimes', 'required', 'array'],
            'mentioned_in_comment.*' => ['string', Rule::in('database')],
        ];
    }

    /**
     * Persist the changes
     *
     * @return void
     */
    public function persist()
    {
        $this->user()->preferences()->merge($this->getPreferences());
    }

    /**
     * Merge the preferences from the request with the missing preferences
     *
     * @return array
     */
    protected function getPreferences()
    {
        return array_merge(
            request()->all(),
            $this->disableMissingPreferences()
        );
    }

    /**
     * Disable the user preferences that are missing from the request
     *
     * @return array
     */
    protected function disableMissingPreferences()
    {
        return collect($this->user()->preferences)
            ->diffKeys(request()->all())
            ->map(function ($value, $key) {
                if (is_array($value)) {
                    return array();
                } elseif (is_bool($value)) {
                    return false;
                }
            })->toArray();
    }
}