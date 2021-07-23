<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

function create($class, $attributes = [])
{
    return $class::factory()->create($attributes);
}

function make($class, $attributes = [])
{
    return $class::factory()->make($attributes);
}

function raw($class, $attributes = [])
{
    return $class::factory()->raw($attributes);
}

function createMany($class, $count, $attributes = [])
{
    return $class::factory()->count($count)->create($attributes);
}

function makeMany($class, $count, $attributes = [])
{
    return $class::factory()->count($count)->make($attributes);
}

function rawMany($class, $count, $attributes = [])
{
    return $class::factory()->count($count)->raw($attributes);
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