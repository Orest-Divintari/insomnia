<?php

namespace App\Filters;

interface FilterInterface
{
    public function setBuilder($builder);
    public function builder();
}