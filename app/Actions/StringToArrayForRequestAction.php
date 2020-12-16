<?php

namespace App\Actions;

use App\Traits\CreatesArrayFromString;
use Illuminate\Http\Request;

class StringToArrayForRequestAction
{
    use CreatesArrayFromString;

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
    protected $values;

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
        $values
    ) {
        $this->request = $request;
        $this->attribute = $attribute;
        $this->values = $values;
    }

    /**
     * Replace the value of the request attribute with an array of values
     *
     * @return void
     */
    public function execute()
    {
        if (!is_string($this->values)) {
            return;
        }

        if (str_contains($this->values, ',')) {
            $this->request->merge(
                [$this->attribute => $this->splitValues($this->values)]
            );
        } else {
            $this->request->merge(
                [$this->attribute => [$this->clean($this->values)]]
            );
        }

        // set the new value of the attribute globally
        // so that all requests now can have the new value
        request()->merge(
            [$this->attribute => $this->request->input($this->attribute)]
        );
    }
}