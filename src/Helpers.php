<?php

namespace Z3d0X\FilamentFabricator;

abstract class Helpers
{
    /**
     * Group an array of associative arrays by a given key
     *
     * @param  array[]  $arr
     * @return array[]
     */
    public static function arrayRefsGroupBy(array &$arr, string $key): array
    {
        $ret = [];

        foreach ($arr as &$item) {
            $ret[$item[$key]][] = &$item;
        }

        return $ret;
    }
}
