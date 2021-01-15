<?php

namespace App\Http\Requests;

use App\Actions\StringToArrayForRequestAction;
use App\Http\Requests\SearchRequestInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SearchPostingsRequest implements SearchRequestInterface
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply validation
     *
     * @return void
     */
    public function validate()
    {
        $this->beforeValidation();

        $validator = Validator::make(
            $this->request->input(),
            $this->rules(),
            $this->messages()
        );

        $this->afterValidation($this->request);

        return $validator;
    }

    public function beforeValidation()
    {

        $action = new StringToArrayForRequestAction(
            $request = $this->request,
            $attribute = 'postedBy',
            $value = $this->request->input('postedBy')
        );
        $action->execute();
    }

    /**
     * Disable title search when no search query is passed
     *
     * @param Request $request
     * @return void
     */
    public function afterValidation()
    {
        // When the search query is empty, there is no point in having onlyTitle equal to true
        // since onlyTitle applies when a thread is being searched given a search query
        if ($this->request->missing('q') && $this->request->boolean('onlyTitle')) {
            $this->request->merge(['onlyTitle' => false]);
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

        if (!$this->request->filled('postedBy')) {
            $rules['q'] = 'required';
        }

        if (!$this->request->filled('q')) {
            $rules['postedBy'] = ['required', "array", 'min:1'];
            $rules['postedBy.*'] = ['required', 'string', 'exists:users,name'];
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
        if (is_null(request('postedBy'))) {
            return $messages;
        }
        foreach (request('postedBy') as $index => $username) {
            if (isset($username) && is_string($username)) {
                $messages["postedBy." . $index . ".exists"] = "The following member could not be found: " . $username;
            }
        }
        return $messages;
    }
}