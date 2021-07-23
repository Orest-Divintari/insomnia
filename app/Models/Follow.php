<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    const FOLLOWINGS_PER_PAGE = 10;
    const FOLLOWERS_BY_PER_PAGE = 10;
}