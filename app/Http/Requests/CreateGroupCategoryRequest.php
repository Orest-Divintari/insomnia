<?php

namespace App\Http\Requests;

use App\GroupCategory;
use Illuminate\Foundation\Http\FormRequest;

class CreateGroupCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:100'],
            'excerpt' => ['required', 'string', 'min:3', 'max:100'],
        ];
    }

    /**
     * Store the new group category in the database
     *
     * @return GroupCategory
     */
    public function persist()
    {
        GroupCategory::create($this->validated());
    }
}