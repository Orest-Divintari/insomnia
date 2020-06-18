<?php

namespace App\Http\Requests;

use App\Thread;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CreateThreadRequest extends FormRequest
{
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
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'category_id' => ['required', 'exists:categories,id', 'integer'],
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
            ]
        ));
    }

}