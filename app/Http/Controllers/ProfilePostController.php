<?php

namespace App\Http\Controllers;

use App\ProfilePost;

class ProfilePostController extends Controller
{
    public function show(ProfilePost $post)
    {
        return redirect($post->path);
    }
}