<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $comment = $this->route('comment');
        return $comment && $this->user()->can('update', $comment);
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
     * Update the given comment
     *
     * @return void
     */
    public function update($comment)
    {
        $comment->update([
            'body' => request('body'),
        ]);
    }
}