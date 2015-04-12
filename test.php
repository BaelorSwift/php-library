<?php

require('vendor/autoload.php');

## TODO

# TestSuit (PHPSpec/PHPUnit)
# Lyrics
# Images
# Documentation

use Duffleman\baelor\BaelorAPI;

$api = new BaelorAPI('{API-HERE}');

$resource = $api->getAlbums('taylor-swift');

die(var_dump($resource));