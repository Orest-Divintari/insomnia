<?php

use Illuminate\Support\Facades\Storage;

function user_avatar($user = null)
{
    return $user ? $user->avatar_path : auth()->user()->avatar_path;
}

function guest_avatar()
{
    return asset('storage/images/avatars/users/guest/guest_logo.png');
}

function to_bool($value)
{
    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
}

function snake_to_camel($string, $capitalizeFirstCharacter = false)
{
    $str = str_replace('_', '', ucwords($string, '_'));

    if (!$capitalizeFirstCharacter) {
        $str = lcfirst($str);
    }

    return $str;
}

function to_camel($string, $capitalizeFirstCharacter = false)
{
    if (str_contains($string, '-')) {
        return kebab_to_camel($string);
    } elseif (str_contains($string, '_')) {
        return snake_to_camel($string);
    }
    return $string;
}

function kebab_to_camel($string, $capitalizeFirstCharacter = false)
{
    $str = str_replace('-', '', ucwords($string, '-'));

    if (!$capitalizeFirstCharacter) {
        $str = lcfirst($str);
    }

    return $str;
}

function random_string($numberOfCharacters)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $string = '';
    do {
        $string = $string . substr(str_shuffle($characters), 0, $numberOfCharacters);
    } while (strlen($string) < $numberOfCharacters);

    return substr($string, 0, $numberOfCharacters);
}