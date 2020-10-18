<?php

namespace App\Search;

use Illuminate\Http\Request;

interface SearchStrategyFactoryInterface
{
    public function create(Request $request);
}