<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    const FOLLOWS_PER_PAGE = 10;
    const FOLLOWED_BY_PER_PAGE = 10;

}