<?php

function create($model, $attributes = [])
{
    return factory("App\Models\{$model}")->create($attributes);
}

function make($model, $attributes = [])
{
    return factory("App\Models\{$model}")->make($attributes);
}