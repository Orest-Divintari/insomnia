<?php

namespace App\Actions;

use App\Traits\CreatesNamesArray;

class CreateNamesArrayAction
{
    use CreatesNamesArray;

    /**
     * The usernames that will be used to create an array
     *
     * @var string
     */
    protected $usernames;

    /**
     * Create a new instance
     *
     * @param string $usernames
     * @param string $usernames
     */
    public function __construct(string $usernames)
    {
        $this->usernames = $usernames;
    }

    /**
     * Crate an array from the given usernames
     *
     * @param string $usernames
     * @return array
     */
    public function execute()
    {
        if (str_contains($this->usernames, ',')) {
            return $this->splitNames($this->usernames);
        } else {
            return array($this->clean($this->usernames));
        }
    }

}