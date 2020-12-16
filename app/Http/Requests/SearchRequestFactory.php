<?php

namespace App\Http\Requests;

use App\Http\Requests\SearchPostingsRequest;
use App\Http\Requests\SearchTagsRequest;
use Illuminate\Http\Request;

class SearchRequestFactory
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function create()
    {
        if ($this->request->input('type') == 'tag') {
            return new SearchTagsRequest($this->request);
        } else {
            return new SearchPostingsRequest($this->request);
        }
    }

}