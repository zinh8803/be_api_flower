<?php

if (!function_exists('make_cookie')) {
    function make_cookie(
        string $name,
        string $value,
        int $minutes,
        bool $httpOnly = true
    ) {
        return cookie(
            $name,
            $value,
            $minutes,
            '/',
            env('COOKIE_DOMAIN', null),
            filter_var(env('COOKIE_SECURE', false), FILTER_VALIDATE_BOOLEAN),
            $httpOnly,
            false, // raw
            env('COOKIE_SAMESITE', 'none')
        );
    }
}
