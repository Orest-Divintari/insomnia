<?php

namespace App\Actions;

use App\Traits\CreatesNamesArray;
use Illuminate\Http\Request;

class CreateNamesArrayForRequestAction
{
    use CreatesNamesArray;

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
}