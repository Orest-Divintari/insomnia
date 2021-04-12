<?php

namespace App\Avatar;

interface AvatarInterface
{
    public function __construct(array $config);
    public function generate($username);
    public function textColor($color);
    public function backgroundColor($color);
    public function fontSize($color);
    public function size($color);
    public function length($color);
}