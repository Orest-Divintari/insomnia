<?php

namespace App\Search;

use Illuminate\Http\Request;

interface SearchStrategyInterface
{
    public function search(Request $request);
}