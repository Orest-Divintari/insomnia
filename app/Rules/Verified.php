<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class Verified implements Rule
{
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
     * The name of the user
     *
     * @var string
     */
    protected $value;

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

        return User::query()
            ->findByName($value)
            ->first()
            ->hasVerifiedEmail();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The following member has not been verified: {$this->value}";
    }
}