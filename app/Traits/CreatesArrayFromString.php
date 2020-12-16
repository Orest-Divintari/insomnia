<?php

namespace App\Traits;

trait CreatesArrayFromString
{
    /**
     * Get an array of the values from the comma separated string
     *
     * @param string $commaSeparatedvalues
     * @return array
     */
    public function splitValues($commaSeparatedValues)
    {
        return $this->discardEmpty(
            $this->createArray($commaSeparatedValues)
        );
    }

    /**
     * Create an array of values from comma separated string
     *
     * @param string $commaSeparatedValues
     * @return string[]
     */
    public function createArray($commaSeparatedValues)
    {
        return array_map(
            fn($value) => $this->clean($value),
            explode(',', $commaSeparatedValues)
        );
    }
    /**
     * Filter out the empty values
     *
     * @param string[] $values
     * @return string[]
     */
    public function discardEmpty($values)
    {
        return array_values(
            array_filter(
                $values,
                fn($value) => !empty($value)
            )
        );
    }

    /**
     * Remove all special characters and white space
     *
     * @param string $value
     * @return string
     */
    public function clean($value)
    {
        return trim($value, ' ');
    }
}