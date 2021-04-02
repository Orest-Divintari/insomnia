<?php

namespace Tests\Setup;

interface FactoryInterface
{
    public function create($attributes = []);
    public function createMany($count = 1, $attributes = []);
}