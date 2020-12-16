<?php

namespace App\Actions;

use App\Traits\CreatesArrayFromString;

class StringToArrayAction
{
    use CreatesArrayFromString;

    /**
     * The string that will be used to create an array
     *
     * @var string
     */
    protected $string;

    /**
     * Create a new instance
     *
     * @param string $string
     */
    public function __construct(string $string)
    {
        $this->string = $string;
    }

    /**
     * Crate an array from the given values
     *
     * @param string $values
     * @return array
     */
    public function execute()
    {
        if (str_contains($this->string, ',')) {
            return $this->splitValues($this->string);
        } else {
            return array($this->clean($this->string));
        }
    }

}