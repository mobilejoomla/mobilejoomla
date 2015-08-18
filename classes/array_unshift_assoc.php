<?php

if (!function_exists('array_unshift_assoc')) {
    /**
     * @param array $arr
     * @param mixed $value
     * @param string|null $key
     */
    function array_unshift_assoc(&$arr, $value, $key = null)
    {
        $arr = array_reverse($arr, true);
        if ($key) {
            $arr[$key] = $value;
        } else {
            $arr[] = $value;
        }
        $arr = array_reverse($arr, true);
    }
}