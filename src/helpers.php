<?php

use Symfony\Component\VarDumper\VarDumper as Dumper;

if ( !function_exists('dd')) {
    function dd()
    {
        array_map(function ($x) {
            (new Dumper)->dump($x);
        }, func_get_args());

        die;
    }
}