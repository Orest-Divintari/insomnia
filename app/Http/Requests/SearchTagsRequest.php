<?php

namespace App\Http\Requests;

use App\Actions\StringToArrayForRequestAction;
use App\Http\Requests\SearchRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SearchTagsRequest extends SearchRequest
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

        return $this->validate();
    }

    /**
     * Create a validator instance and validate the request
     *
     * @return Validator
     */
    protected function validate()
    {
        return Validator::make(
            $this->request->input(),
            $this->rules(),
            $this->messages()
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param Request $request
     * @return array
     */
    protected function rules()
    {
        return [
            'q' => ['array', 'required', 'min:1'],
            'q.*' => ['required', 'string', 'exists:tags,name'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    protected function messages()
    {
        $messages = [
            'q.*.required' => 'Please anter at least one tag',
        ];

        foreach ($this->request->input('q') as $index => $tag) {
            if (isset($tag) && is_string($tag)) {
                $messages["q." . $index . ".exists"] = "The following tag could not be found: " . $tag;
            }
        }

        return $messages;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $action = new StringToArrayForRequestAction(
            $request = $this->request,
            $attribute = 'q',
            $value = $this->request->input('q')
        );
        $action->execute();
    }
}