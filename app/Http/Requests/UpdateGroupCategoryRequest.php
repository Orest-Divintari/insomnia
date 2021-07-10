<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupCategoryRequest extends FormRequest
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
     * Update the given group category
     *
     * @param GroupCategory $groupCategory
     * @return void
     */
    public function update($groupCategory)
    {
        $groupCategory->update($this->validated());
    }
}