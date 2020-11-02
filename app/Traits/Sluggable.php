<?php

namespace App\Traits;

trait Sluggable
{
    /**
     * Assert the slug is unique
     *
     * @param string $slug
     * @return void
     */
    public function setSlugAttribute($slug)
    {
        if (static::where('slug', $slug)->exists()) {
            $slug = $this->createUniqueSlug($slug);
        }
        $this->attributes['slug'] = $slug;

    }

    /**
     * Create a unique slug
     *
     * @param string $slug
     * @return string $slug
     */
    protected function createUniqueSlug($slug)
    {
        $counter = 2;
        $originalSlug = $slug;
        while (static::whereSlug($slug)->exists()) {

            $slug = "{$originalSlug}.{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Get the route key name
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}