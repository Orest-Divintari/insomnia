<?php

namespace App\Http\Requests;

use App\Thread;
use Illuminate\Foundation\Http\FormRequest;

class UpdateThreadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $thread = $this->route('thread');
        return $thread && $this->user()->can('manage', $thread);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => ['required', 'string'],
        ];
    }

    /**
     * Update an existing thread
     *
     * @param Thread $thread
     * @return void
     */
    public function update(Thread $thread)
    {
        $thread->update($this->validated());
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => 'Please enter a valid title.',
            'title.string' => 'Please enter a valid title.',
        ];
    }
}