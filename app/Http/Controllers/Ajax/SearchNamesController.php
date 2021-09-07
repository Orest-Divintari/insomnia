<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Search\SearchNames;

class SearchNamesController extends Controller
{
    /**
     * Return the search results
     *
     * @param string $name
     * @param SearchNames $searchNames
     * @return \Illuminate\Http\Response
     */
    public function index($name, SearchNames $searchNames)
    {
        return $searchNames->handle($name);
    }

}