<?php

namespace App\Http\Controllers;

use App\Tag;

class TagController extends Controller
{
    /**
     * Display the tag
     *
     * @param Tag $tag
     * @return View
     */
    public function show(Tag $tag)
    {
        $tag->load([
            'threads.tags',
            'threads.category',
            'threads.poster',
        ]);
        return view('tags.show', compact('tag'));
    }
}