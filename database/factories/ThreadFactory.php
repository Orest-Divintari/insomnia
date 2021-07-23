<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ThreadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Thread::class;

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
            'body' => $this->faker->paragraph(),
            'user_id' => function () {
                return auth()->id() ?: User::factory()->create()->id;
            },
            'category_id' => function () {
                return Category::factory()->create()->id;
            },
            'pinned' => false,
            'locked' => false,
            'replies_count' => 0,
            'views' => 0,
        ];

    }
}