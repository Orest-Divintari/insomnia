<?php

namespace App\Http\Requests;

use App\Actions\StringToArrayForRequestAction;
use App\Thread;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CreateThreadRequest extends FormRequest
{
    /**
     * The error message for an invalid body
     *
     * @var string
     */
    protected $bodyErrorMessage = 'Please enter a valid message.';

    /**
     * The error message for an invalid title
     *
     * @var string
     */
    protected $titleErrorMessage = 'Please enter a valid title.';

    /**
     * The error message for an invalid category
     *
     * @var string
     */
    protected $categoryErrorMessage = "Please enter a valid category.";

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation()
    {
        $action = new StringToArrayForRequestAction(
            $request = $this,
            $attribute = 'tags',
            $value = $this->input('tags')
        );
        $action->execute();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'body' => ['required', 'string'],
            'title' => ['required', 'string'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
        ];

        if (request()->filled('tags')) {
            $rules['tags'] = ['sometimes', 'required', 'array'];
            $rules['tags.*'] = ['sometimes', 'required', 'string', 'exists:tags,name'];
        }

        return $rules;
    }

    /**
     * Create a new row with the validated data
     *
     * @return void
     */
    public function persist()
    {
        $thread = Thread::create(array_merge(
            $this->validated(),
            [
                'user_id' => $this->user()->id,
                'slug' => Str::slug($this->input('title')),
                'replies_count' => 0,
            ]
        ));

        if (request()->filled('tags')) {
            $thread->addTags(request('tags'));
        }

        return $thread;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $messages = [
            'body.string' => $this->bodyErrorMessage,
            'body.required' => $this->bodyErrorMessage,
            'title.required' => $this->titleErrorMessage,
            'title.string' => $this->titleErrorMessage,
            'category_id.required' => $this->categoryErrorMessage,
            'category_id.exists' => $this->categoryErrorMessage,
            'category_id.integer' => $this->categoryErrorMessage,
        ];

        $messages = $this->addTagExistsMessage($messages);
        return $messages;
    }

    /**
     * Add the message for tags.*.exists rule
     *
     * @param array $messages
     * @return array
     */
    public function addTagExistsMessage($messages)
    {
        if (is_null(request('tags'))) {
            return $messages;
        }

        foreach (request('tags') as $index => $tag) {
            if (isset($tag) && is_string($tag)) {
                $messages["tags." . $index . ".exists"] = "The following tag could not be found: " . $tag;
            }
        }
        return $messages;
    }

}