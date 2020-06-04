<?php

namespace App\Http\Controllers;

use App\Category;

class ThreadController extends Controller
{
    public function index(Category $category)
    {

        return view('threads.index', ['threads' => $category->threads]);
    }
}