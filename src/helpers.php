<?php

use Symfony\Component\VarDumper\VarDumper as Dumper;

if ( !function_exists('dd')) {
    /**
     * Die and Dump.
     *
     * Shouldn't be needed if you pull in illuminate\support.
     */
    function dd()
    {
        array_map(function ($x) {
            (new Dumper)->dump($x);
        }, func_get_args());

        die;
    }
}