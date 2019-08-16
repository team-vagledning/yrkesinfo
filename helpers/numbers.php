<?php

/**
 * Round and format number to precision
 */
if (!function_exists('round_number')) {
    function round_number($value, $precision = 2)
    {
        return number_format(round($value, $precision), $precision, '.', '');
    }
}