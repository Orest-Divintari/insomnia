<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class GravatarExists implements Rule
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
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $email)
    {
        $gravemail = md5(strtolower(trim($email)));
        $gravsrc = "http://www.gravatar.com/avatar/" . $gravemail;
        $gravcheck = "http://www.gravatar.com/avatar/" . $gravemail . "?d=404";
        $response = get_headers($gravcheck);

        return !str_contains($response[0], '404 Not Found');
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Gravatars require valid email addresses.';
    }
}