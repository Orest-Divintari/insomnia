<?php

namespace App\Http\Requests;

use App\Actions\StringToArrayForRequestAction;
use App\Conversation;
use Illuminate\Foundation\Http\FormRequest;

class InviteConversationParticipant extends FormRequest
{
    public $conversation;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->conversation = Conversation::whereSlug($this->route('conversation'))
            ->first();

        return $this->conversation && $this->user()->can('manage', $this->conversation);
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation()
    {
        $action = new StringToArrayForRequestAction(
            $request = $this,
            $attribute = 'participants',
            $value = $this->input('participants')
        );
        $action->execute();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'participants' => ['required', "array", 'min:1'],
            'participants.*' => ['required', 'string', 'exists:users,name'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $messages = [
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
                $messages["participants." . $index . ".exists"] = "You may not start a conversation with the following participant: " . $participant;
            }
        }
        return $messages;
    }

}