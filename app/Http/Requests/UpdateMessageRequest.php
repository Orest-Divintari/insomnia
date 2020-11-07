<?php

namespace App\Http\Requests;

use App\Reply;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $message = $this->route('message');
        return $message && $this->user()->can('update', $message);
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

    public function update(Reply $message)
    {
        $message->update(
            $this->validated()
        );
    }
}