<?php

namespace App\Http\Requests;

use App\Http\Requests\SearchRequestFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SearchRequest
{

    /**
     * The resulting validation instance
     *
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * Get the required search request and apply validation
     *
     * @return SearchRequest
     */
    public function handle($request)
    {
        $searchRequest = $this->getSearchRequest($request);

        $this->validator = $searchRequest->validate();

        return $this;
    }

    /**
     * Get the required search request
     *
     * @param Request $request
     * @return SearchRequestAbstractClass
     */
    public function getSearchRequest($request)
    {
        return (new SearchRequestFactory($request))->create();
    }

    /**
     * Get the validator instance
     *
     * @return \Illuminate\Validation\Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Get search query
     *
     * @return string
     */
    public function getSearchQuery()
    {
        if (is_null(request('q'))) {
            return "";
        }
        if (is_string(request('q'))) {
            return request('q');
        }
        return implode(', ', request('q'));
    }

}