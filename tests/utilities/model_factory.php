<?php

use App\User;
use Spatie\Permission\Models\Role;

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

function createAdminUser()
{
    $user = create(User::class);

    if (!Role::where('name', 'admin')->exists()) {
        Role::create(['name' => 'admin']);
    }
    $user->assignRole('admin');
    return $user;
}