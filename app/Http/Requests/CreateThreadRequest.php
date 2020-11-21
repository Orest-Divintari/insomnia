<?php

namespace App\Http\Requests;

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
    protected $categoryErrorMessage = "Please enter a valid category";

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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body' => ['required', 'string'],
            'title' => ['required', 'string'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
        ];
    }

    /**
     * Create a new row with the validated data
     *
     * @return void
     */
    public function persist()
    {
        return Thread::create(array_merge(
            $this->validated(),
            [
                'user_id' => $this->user()->id,
                'slug' => Str::slug($this->input('title')),
                'replies_count' => 0,
            ]
        ));
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'body.string' => $this->bodyErrorMessage,
            'body.required' => $this->bodyErrorMessage,
            'title.required' => $this->titleErrorMessage,
            'title.string' => $this->titleErrorMessage,
            'category_id.required' => $this->categoryErrorMessage,
            'category_id.exists' => $this->categoryErrorMessage,
            'category_id.integer' => $this->categoryErrorMessage,
        ];
    }

}