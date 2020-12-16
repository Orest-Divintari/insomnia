<?php

namespace App\Http\Requests;

use App\Actions\StringToArrayForRequestAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SearchTagsRequest implements SearchRequestInterface
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply validation
     *
     * @return Validator
     */
    public function validate()
    {
        $this->prepareForValidation();

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
    public function rules()
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
    public function messages()
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
    public function prepareForValidation()
    {
        $action = new StringToArrayForRequestAction(
            $request = $this->request,
            $attribute = 'q',
            $value = $this->request->input('q')
        );
        $action->execute();
    }
}