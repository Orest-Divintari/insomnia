<?php

namespace App\Http\Requests;

use App\Thread;
use Illuminate\Foundation\Http\FormRequest;

class PostReplyRequest extends FormRequest
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
            'body' => 'required',
        ];
    }

    /**
     * Persist the newly posted reply in the database
     *
     * @param Thread $thread
     * @return void
     */
    public function persist(Thread $thread)
    {
        return $thread->replies()->create(
            $this->validated() + ['user_id' => $this->user()->id]
        );

    }
}