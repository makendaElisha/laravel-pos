<?php
if (!function_exists('activeSegment')) {
    function activeSegment($name, $segment = 2, $class = 'active')
    {
        return request()->segment($segment) == $name ? $class : '';
    }
}

if (!function_exists('posprice')) {
    function posprice($amount)
    {
        return number_format($amount, 0, ',', '.');
    }
}
