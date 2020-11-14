<?php

namespace App\Exceptions;

use Exception;

class SearchResultsNotFound extends Exception
{
    public function render()
    {
        return response('No results found.', 404);
    }
}