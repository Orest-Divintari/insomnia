<?php

namespace App\Http\Requests;

use App\Actions\StringToArrayForRequestAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SearchPostingsRequest extends SearchRequest
{
    protected $request;

    /**
     * Create a new instance
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle validation
     *
     * @var \Illuminate\Validation\Validator
     */
    public function handle()
    {
        $this->prepareForValidation();

        $validator = $this->validate();

        $this->afterValidation($this->request);

        return $validator;
    }

    /**
     * Create a validator instance and validate the request
     *
     * @var \Illuminate\Validation\Validator
     */
    public function validate()
    {
        return Validator::make(
            $this->request->input(),
            $this->rules(),
            $this->messages()
        );
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation()
    {
        $action = new StringToArrayForRequestAction(
            $request = $this->request,
            $attribute = 'posted_by',
            $value = $this->request->input('posted_by')
        );

        $action->execute();
    }

    /**
     * Handle after validation
     *
     * @param Request $request
     * @return void
     */
    public function afterValidation()
    {
        // When the search query is empty, there is no point in having onlyTitle equal to true
        // since onlyTitle applies when a thread is being searched given a search query
        if ($this->request->missing('q') && $this->request->boolean('only_title')) {
            $this->request->merge(['only_title' => false]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param Request $request
     * @return array
     */
    public function rules()
    {
        $rules = [];

        if (!$this->request->filled('posted_by')) {
            $rules['q'] = 'required';
        }

        if (!$this->request->filled('q')) {
            $rules['posted_by'] = ['required', "array", 'min:1'];
            $rules['posted_by.*'] = ['required', 'string', 'exists:users,name'];
        }

        $rules['type'] = [
            'sometimes',
            'required',
            'string',
            Rule::in(['thread', 'profile_post', 'tag']),
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
        $messages = [
            'q.required' => 'Please specify a search query or the name of a member.',
            'type.in' => 'The following search type could not be found: ' . request('type'),
        ];

        $messages = $this->addPostedByExistsMessage($messages);
        return $messages;

    }

    /**
     * Add the message for postedBy.*.exists rule
     *
     * @param string[] $messages
     * @return string[]
     */
    public function addPostedByExistsMessage($messages)
    {
        if (is_null(request('posted_by'))) {
            return $messages;
        }
        foreach (request('posted_by') as $index => $username) {
            if (isset($username) && is_string($username)) {
                $messages["posted_by." . $index . ".exists"] = "The following member could not be found: " . $username;
            }
        }
        return $messages;
    }
}