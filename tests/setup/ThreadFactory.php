<?php

namespace Tests\Setup;

use App\Category;
use App\Thread;
use Faker\Generator as Faker;
use Tests\Setup\PostFactory;

class ThreadFactory extends PostFactory
{
    protected $category;
    protected $title;

    public function create($attributes = [])
    {
        $this->attributes = $attributes;
        $thread = factory(Thread::class)->create(
            array_merge(
                [
                    'user_id' => $this->userId(),
                    'category_id' => $this->categoryId(),
                    'created_at' => $this->getCreatedAt(),
                    'updated_at' => $this->getUpdatedAt(),
                    'title' => $this->getTitle(),
                    'body' => $this->getBody(),
                ],
                $attributes
            ));
        $this->resetAttributes();
        return $thread;
    }

    public function createMany($count = 1, $attributes = [])
    {
        $this->attributes = $attributes;
        $threads = factory(Thread::class, $count)->create(
            array_merge(
                [
                    'user_id' => $this->userId(),
                    'category_id' => $this->categoryId(),
                    'created_at' => $this->getCreatedAt(),
                    'updated_at' => $this->getUpdatedAt(),
                    'title' => $this->getTitle(),
                ],
                $attributes
            ));
        $this->resetAttributes();
        return $threads;
    }

    private function getTitle()
    {
        return $this->title ?? app(Faker::class)->sentence();
    }

    private function categoryId()
    {
        if (array_key_exists('category_id', $this->attributes)) {
            return;
        }
        return $this->category->id ?? factory(Category::class)->create()->id;
    }

    public function withTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function inCategory($category)
    {
        $this->category = $category;
        return $this;
    }

}