<?php

namespace App\Traits;

trait CreatesNamesArray
{
    /**
     * Get an array of the names from the comma separated string
     *
     * @param string $commaSeparatedNames
     * @return array
     */
    public function splitNames($commaSeparatedNames)
    {
        return $this->discardEmpty(
            $this->createNamesArray($commaSeparatedNames)
        );
    }

    /**
     * Create an array of names from comma separated string
     *
     * @param string $commaSeparatedNames
     * @return string[]
     */
    public function createNamesArray($commaSeparatedNames)
    {
        return array_map(
            fn($name) => $this->clean($name),
            explode(',', $commaSeparatedNames)
        );
    }
    /**
     * Filter out the empty names
     *
     * @param string[] $names
     * @return string[]
     */
    public function discardEmpty($names)
    {
        return array_values(
            array_filter(
                $names,
                fn($name) => !empty($name)
            )
        );
    }

    /**
     * Remove all special characters and white space
     *
     * @param string $name
     * @return string
     */
    public function clean($name)
    {
        return trim($name, ' ');
    }
}