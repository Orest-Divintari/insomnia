<?php

function create($class, $attributes = [])
{
    return factory($class)->create($attributes);
}

function make($class, $attributes = [])
{
    return factory($class)->make($attributes);
}

function raw($class, $attributes = [])
{
    return factory($class)->raw($attributes);
}

function createMany($class, $count, $attributes = [])
{
    return factory($class, $count)->create($attributes);
}

function makeMany($class, $count, $attributes = [])
{
    return factory($class, $count)->make($attributes);
}

function rawMany($class, $count, $attributes = [])
{
    return factory($class, $count)->raw($attributes);
}