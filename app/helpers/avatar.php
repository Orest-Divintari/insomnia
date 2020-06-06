<?php

function user_avatar($user = null)
{
    return $user ? $user->avatar_path : auth()->user()->avatar_path;
}