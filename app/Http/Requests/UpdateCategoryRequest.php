<?php

namespace App\Http\Requests;

use App\Category;
use App\Rules\SameWithParentCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UpdateCategoryRequest extends FormRequest
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
     * Update the given category
     *
     * @param Category $category
     * @return Category
     */
    public function update(Category $category)
    {
        $attributes = $this->prepareForUpdate($category, $this->validated());

        $category->update($attributes);
    }

    /**
     * Store image and update category's image path
     *
     * @param Category $category
     * @return string
     */
    protected function storeImage($category)
    {
        return $this->file('image_path')
            ->store("/images/categories/{$category->id}/image");
    }

    /**
     * Prepare data for update
     *
     * @param Category $category
     * @param array $validatedAttributes
     * @return array
     */
    private function prepareForUpdate(Category $category, $validatedAttributes)
    {
        if ($this->hasFile('image_path')) {
            $this->deleteExistingImage($category);
            $imagePath = $this->storeImage($category);
            $validatedAttributes['image_path'] = $imagePath;
        }

        $validatedAttributes['slug'] = Str::slug($validatedAttributes['title']);

        return $validatedAttributes;
    }

    /**
     * Delete existing image of the given category
     *
     * @param Category $category
     * @return void
     */
    protected function deleteExistingImage($category)
    {
        Storage::disk('public')->delete($category->image_path);
    }
}