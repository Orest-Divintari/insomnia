<?php

namespace App\Http\Requests;

use App\Actions\CreateNamesArrayForRequestAction;
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
            'title' => ['required', 'string'],
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
        $action = new CreateNamesArrayForRequestAction(
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
                'user_id' => $this->user()->id,
                'title' => $title,
                "slug" => $slug,
            ]
        );
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $messages = [
            'title.required' => 'Please enter a valid title.',
            'title.string' => 'Please enter a valid title.',
            'message.required' => 'Please enter a valid message.',
            'message.string' => 'Please enter a valid message.',
            'participants.required' => 'Please enter at least one username.',
            'participants.min' => 'Please enter at least one username.',
        ];
        $messages = $this->addParticipantExistsMessage($messages);

        return $messages;
    }

    /**
     * Add the message for participant.*.exists rule
     *
     * @param array $messages
     * @return array
     */
    public function addParticipantExistsMessage($messages)
    {
        if (is_null(request('participants'))) {
            return $messages;
        }

        foreach (request('participants') as $index => $participant) {
            if (isset($participant) && is_string($participant)) {
                $messages["participants." . $index . ".exists"] = "The following participant could not be found: " . $participant;
            }
        }
        return $messages;
    }
}