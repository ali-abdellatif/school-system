<?php

use App\Support\SchoolConfig;

if (! function_exists('school')) {
    function school(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return SchoolConfig::all();
        }

        return SchoolConfig::get($key, $default);
    }
}
