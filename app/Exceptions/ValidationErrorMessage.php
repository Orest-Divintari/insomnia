<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Str;

class ValidationErrorMessage
{
    /**
     * The validation exception
     *
     * @var Exception
     */
    protected $exception;

    /**
     * The alternative name of attributes
     * that represent users that need to be validated
     *
     * @var array
     */
    protected $userAttributes = ['participants', 'members'];

    /**
     * The name of the current attribute that represents the user
     *
     * @var string
     */
    protected $userAttribute;

    /**
     * The error message for the current attribute/rule
     * that comes from the form request
     *
     * @var string
     */
    protected $userAttributeMessage = "";

    /**
     * The new error messages that will be returned
     *
     * @var array
     */
    protected $errorMessages = [];

    /**
     * Create a new instance
     *
     * @param Exception $exception
     */
    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * Get the errors and the corresponding messages from the validation
     *
     * @return array
     */
    public function getMessages()
    {
        $this->findUserAttributeAndMessage();
        if ($this->userAttributeExists()) {
            $this->addUserError();
        }

        foreach ($this->exception->errors() as $error => $messages) {
            $this->addError($error, $messages);
        }

        return $this->errorMessages;
    }

    /**
     * Determine if the current validation contains a rule for users
     *
     * @return bool
     */
    public function userAttributeExists()
    {
        return isset($this->userAttribute);
    }
    /**
     * Add the error for the current user attribute and the corresponding message
     * concatenated with usernames that failed validation
     *
     * @return void
     */
    public function addUserError()
    {
        // concat the message and the usernames that failed the validation
        $this->errorMessages[$this->userAttribute] = array(
            $this->userAttributeMessage . ' ' . $this->invalidUsernames(),
        );
    }

    /**
     * Set the errors and corresponding messages that occurred from the validation
     * except the error that is concerned with the user attribute
     *
     * @param string $error
     * @param array $messages
     * @return void
     */
    public function addError($error, $messages)
    {
        if (!Str::startsWith($error, $this->userAttribute)) {
            $this->errorMessages[$error] = $messages;
        }
    }

    /**
     * Set the current user attribute and the corresponding error messages
     *
     * @return void
     */
    public function findUserAttributeAndMessage()
    {
        foreach ($this->exception->errors() as $error => $messages) {
            foreach ($this->userAttributes as $userAttribute) {
                // if one of the known user attributes is containted in the exception errors
                // grab that attribute and the corresponding message
                // which are defined in the form request
                if (Str::startsWith($error, $userAttribute)) {
                    $this->userAttribute = $userAttribute;
                    $this->userAttributeMessage = $messages[0];
                    return;
                }
            }
        }
    }

    /**
     * Get the usernames that failed the validation
     * separated by comma
     *
     * @return string
     */
    public function invalidUsernames()
    {
        return implode(
            ', ',
            $this->exception
                ->validator
                ->invalid()[$this->userAttribute]
        );
    }
}