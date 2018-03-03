<?php
/**
 * Created by PhpStorm.
 * User: ecarx
 * Date: 2018/2/28
 * Time: 19:48
 */

/**
 * @param array $arr
 * @param $key
 * @param null $default
 * @return mixed|null
 */
function array_get(array $arr, $key, $default = null)
{
    return isset($arr[$key]) ? $arr[$key] : $default;
}