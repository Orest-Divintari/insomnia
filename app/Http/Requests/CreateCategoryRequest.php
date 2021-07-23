<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Rules\SameWithParentCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CreateCategoryRequest extends FormRequest
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
            'parent_id' => ['nullable', 'int', 'exists:categories,id'],
            'group_category_id' => ['required', 'int', 'exists:group_categories,id', new SameWithParentCategory],
            'image_path' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10000'],
        ];
    }

    /**
     * Store the new category
     *
     * @return Category
     */
    public function persist()
    {
        $attributes = $this->prepareForStorage($this->validated());

        $category = Category::create($attributes);

        $this->storeImage($category);

        return $category;
    }

    /**
     * Store image and update category's image path
     *
     * @param Category $category
     * @return void
     */
    protected function storeImage($category)
    {
        $image = $this->file('image_path')
            ->store("/images/categories/{$category->id}/image");

        $category->update(['image_path' => $image]);
    }

    /**
     * Prepare data for storage
     *
     * @param array $validatedAttributes
     * @return array
     */
    private function prepareForStorage($validatedAttributes)
    {
        unset($validatedAttributes['image_path']);
        $attributes['slug'] = Str::slug($validatedAttributes['title']);
        return $validatedAttributes;
    }
}
