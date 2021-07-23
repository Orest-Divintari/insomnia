<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\GroupCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->sentence();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => $this->faker->sentence(),
            'parent_id' => null,
            'group_category_id' => function () {
                return GroupCategory::factory()->create()->id;
            },
            'image_path' => null,
        ];
    }
}