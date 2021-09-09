<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;

abstract class SearchRequest
{
    /**
     * Create a new instance
     *
     * @param Request $request
     */
    abstract public function __construct(Request $request);

    /**
     * Create a validator instance and validate the request
     *
     * @var \Illuminate\Validation\Validator
     */
    abstract protected function validate();

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    abstract protected function messages();

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract protected function rules();

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        //
    }

    /**
     * Handle after validation
     *
     * @return void
     */
    protected function afterValidation()
    {
        //
    }

    /**
     * Handle validation
     *
     * @var \Illuminate\Validation\Validator
     */
    public function handle()
    {
        return $this->validate();
    }

    /**
     * Get search query
     *
     * @return string
     */
    public function query()
    {
        if (is_null(request('q'))) {
            return "";
        }
        if (is_string(request('q'))) {
            return request('q');
        }
        return implode(', ', request('q'));
    }
};