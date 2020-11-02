<?php

namespace App\Http\Requests;

use App\Actions\CreateNamesArrayAction;
use App\Conversation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CreateConversationRequest extends FormRequest
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
            'title' => ['required', 'string', 'min:3'],
            'message' => ['required', 'string'],
            'participants' => ['required', "array", 'min:1'],
            'participants.*' => ['required', 'string', 'exists:users,name'],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation()
    {
        $action = new CreateNamesArrayAction(
            $request = $this,
            $attribute = 'participants',
            $value = $this->input('participants')
        );
        $action->execute();
    }

    /**
     * Create a new conversation
     *
     * @return Conversation
     */
    public function persist()
    {
        $title = $this->input('title');
        $slug = Str::slug($title);
        return Conversation::create(
            [
                'title' => $title,
                "slug" => $slug,
            ]
        );
    }

}