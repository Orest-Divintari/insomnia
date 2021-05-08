<?php

function user_avatar($user = null)
{
    return $user ? $user->avatar_path : auth()->user()->avatar_path;
}

function guest_avatar()
{
    return asset('/images/avatars/users/guest/guest_logo.png');
}

function str_to_bool($value)
{
    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
}