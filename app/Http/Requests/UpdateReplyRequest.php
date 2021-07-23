<?php

namespace App\Http\Requests;

use App\Models\Reply;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReplyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $reply = $this->route('reply');
        return $reply && $this->user()->can('update', $reply);

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body' => ['string', 'required'],
        ];
    }

    /**
     * Update the specific reply
     *
     * @param Reply $reply
     * @return void
     */
    public function update(Reply $reply)
    {
        $reply->update(
            $this->validated()
        );
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
