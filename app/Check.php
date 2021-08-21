<?php

namespace App;

class Check
{
    private $properties;

    public function __construct($properties)
    {
        foreach ($properties as $property => $value) {
            $this->{$property} = $value;
        }
    }

    // public function __get($key): mixed
    // {
    //     if ($this->has($key)) {
    //         return $this->get($key);
    //     } else {
    //         //
    //     }
    // }

    // /**
    //  * @param string $key
    //  * @return mixed
    //  */
    // private function get($key): mixed
    // {
    //     return $this->properties[$key];
    // }

    // /**
    //  * @param string $key
    //  * @return bool
    //  */
    // private function has($key): bool
    // {
    //     return array_key_exists($key, $this->properties);
    // }
}