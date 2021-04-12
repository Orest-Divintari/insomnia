<?php

namespace App\Avatar;

use App\Avatar\AvatarInterface;

class Avatar implements AvatarInterface
{
    protected $backgroundColors = [];
    protected $backgroundColor;
    protected $size;
    protected $fontSize;
    protected $length;
    protected $textColor;

    public function __construct(array $config)
    {
        $this->backgroundColors = $config['backgroundColors'];
        $this->size = $config['size'];
        $this->length = $config['length'];
        $this->textColor = $config['textColor'];
        $this->fontSize = $config['fontSize'];
    }

    public function generate($username)
    {
        $avatarUrl = 'https://eu.ui-avatars.com/api/';

        $options = $this->buildOptions($username);

        return $avatarUrl . $options;
    }

    protected function buildOptions($username)
    {
        return http_build_query([
            'name' => $username,
            'size' => $this->size,
            'font-size' => $this->fontSize,
            'color' => $this->textColor,
            'background' => $this->getBackgroundColor($username),
            'length' => $this->length,
        ]);
    }

    public function textColor($color)
    {
        $this->textColor = $color;
        return $this;
    }

    public function backgroundColor($color)
    {
        $this->backgroundColor = $color;
        return $this;
    }

    public function fontSize($size)
    {
        $this->fontSize = $size;
        return $this;
    }

    public function size($pixels)
    {
        $this->size = $pixels;
        return $this;
    }

    public function length($length)
    {
        $this->length = $length;
        return $this;
    }

    protected function getBackgroundColor($username)
    {
        if ($this->backgroundColor) {
            return $this->backgroundColor;
        }

        if (strlen($username) === 0) {
            $username = chr(rand(65, 90));
        }

        $number = ord($username[0]);
        $i = 1;
        $charLength = strlen($username);
        while ($i < $charLength) {
            $number += ord($username[$i]);
            $i++;
        }

        return $this->backgroundColors[$number % count($this->backgroundColors)];
    }
}