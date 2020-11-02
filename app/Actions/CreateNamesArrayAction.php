<?php

namespace App\Actions;

use Illuminate\Http\Request;

class CreateNamesArrayAction
{

    /**
     * The name of the request parameter
     *
     * @var string
     */
    protected $attribute;
    /**
     * The value of the request parameter
     *
     * @var string
     */
    protected $names;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Create a new instance
     *
     * @param Request $request
     * @param String $attribute
     * @param mixed $names
     */
    public function __construct(
        Request $request,
        String $attribute,
        $names
    ) {
        $this->request = $request;
        $this->attribute = $attribute;
        $this->names = $names;
    }

    /**
     * Replace the value of the request attribute with an array of values
     *
     * @return void
     */
    public function execute()
    {
        if (!is_string($this->names)) {
            return;
        }

        if (str_contains($this->names, ',')) {
            $this->request->merge(
                [$this->attribute => $this->splitNames($this->names)]
            );
        } else {
            $this->request->merge(
                [$this->attribute => [$this->clean($this->names)]]
            );
        }

        // set the new value of the attribute globally
        // so that all requests now can have the new value
        request()->merge(
            [$this->attribute => $this->request->input($this->attribute)]
        );
    }

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