<?php

/**
 * Increment a value (int) in an array
 */
if (!function_exists('data_inc')) {
    function data_inc(&$target, $key, $value)
    {
        $currentValue = data_get($target, $key, 0);
        data_set($target, $key, $currentValue + $value);
    }
}
