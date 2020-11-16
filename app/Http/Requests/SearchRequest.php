<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SearchRequest
{
    /**
     * The resulting validation instance
     *
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * Apply validation
     *
     * @return SearchRequest
     */
    public function validate($request)
    {

        $this->validator = Validator::make(
            $request->input(),
            $this->rules($request),
            $this->messages()
        );
        $this->afterValidation($request);

        return $this;
    }

    /**
     * Disable title search when no search query is passed
     *
     * @param Request $request
     * @return void
     */
    public function afterValidation($request)
    {
        // When the search query is empty, there is no point in having onlyTitle equal to true
        // since onlyTitle applies when a thread is being searched given a search query
        if ($request->missing('q') && $request->boolean('onlyTitle')) {
            $request->merge(['onlyTitle' => false]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param Request $request
     * @return array
     */
    public function rules(Request $request)
    {
        $rules = [];

        if (!$request->filled('postedBy')) {
            $rules['q'] = 'required';
        }

        if (!$request->filled('q')) {
            $rules['postedBy'] = ['required', 'exists:users,name'];
        }
        $rules['type'] = [
            'sometimes',
            'string',
            Rule::in(['thread', 'profile_post']),
        ];
        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'postedBy.exists' => 'The following members could not be found: ' . request('postedBy'),
            'q.required' => 'Please specify a search query or the name of a member.',
            'type.in' => 'The following search type could not be found: ' . request('type'),
        ];
    }

    /**
     * Get the validator instance
     *
     * @return \Illuminate\Validation\Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

}