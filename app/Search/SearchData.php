<?php

namespace App\Search;

use Illuminate\Http\Request;
use Spatie\DataTransferObject\DataTransferObject;

class SearchData extends DataTransferObject
{
    /**
     * The type of model to be searched
     *
     * @var string
     */
    public $type;

    /**
     * Determine whether only titles should be searched
     *
     * @var boolean
     */
    public $onlyTitle;

    /**
     * The seach query
     *
     * @var string|string[]
     */
    public $query;

    /**
     * Create a new instance from request
     *
     * @param Request $request
     * @return SearchData
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'type' => $request->input('type') ?: '',
            'onlyTitle' => $request->boolean('only_title') ?: false,
            'query' => $request->input('q') ?: '*',
        ]);
    }
}