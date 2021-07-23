<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class AllowsConversations implements Rule
{
    protected $value;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
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

        $participant = User::findByName($value)->first();

        return $participant && $participant->allows('start_conversation');
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "You may not start a conversation with the following particpant: {$this->value}";
    }
}