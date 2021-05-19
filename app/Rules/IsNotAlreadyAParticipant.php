<?php

namespace App\Rules;

use App\Conversation;
use App\User;
use Illuminate\Contracts\Validation\Rule;

class IsNotAlreadyAParticipant implements Rule
{
    protected $value;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->value = $value;

        $conversation = $this->getConversation();

        return !$conversation->hasParticipant($this->getUser($value));
    }

    /**
     * Fetch the converastion
     *
     * @return Conversation
     */
    protected function getConversation()
    {
        return Conversation::whereSlug(request('conversation'))->firstOrFail();
    }

    /**
     * Fetch the user
     *
     * @param string $name
     * @return User
     */
    protected function getUser($name)
    {
        return User::findByName($name)->firstOrFail();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "You may not start a conversation with the following recipients: {$this->value}.";
    }
}