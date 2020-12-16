<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;

interface SearchRequestInterface
{
    public function __construct(Request $request);
    public function validate();

};