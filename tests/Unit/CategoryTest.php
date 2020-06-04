<?php

namespace Tests\Unit;

use App\Category;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * set up category test
     *
     * Create a Category instance everytime we run a test
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->category = create(Category::class);
    }

    /** @test */
    public function a_category_has_a_path()
    {

        $this->assertEquals("/forum/categories/{$this->category->slug}", $this->category->path());
    }

    /** @test  */
    public function a_category_belongs_to_a_parent_category()
    {

        $subCategory = create(Category::class, ['parent_id' => $this->category->id]);
        $this->assertInstanceOf(Category::class, $subCategory->parent);
        $this->assertEquals($this->category->id, $subCategory->parent->id);
    }

    /** @test */
    public function a_category_might_have_sub_categories()
    {
        $subCategory = create(Category::class, ['parent_id' => $this->category->id]);
        $this->assertInstanceOf(Category::class, $subCategory->parent);
        $this->assertFalse($this->category->children->contains($this->category->id));
        $this->assertEquals($subCategory->id, $this->category->children->first()->id);
    }

    /** @test */
    public function a_category_has_threads()
    {
        $thread = create(Thread::class, [
            'category_id' => $this->category->id,
        ]);
        $this->assertCount(1, $this->category->threads);
    }
}