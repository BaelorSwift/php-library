<?php

require('vendor/autoload.php');

### TODO in order
# Lyrics
# - Lyric Functions
# Images
# Refactoring
# TestSuite (PHPSpec/PHPUnit)
# Documentation
###

use Duffleman\baelor\BaelorAPI;

$api = new BaelorAPI('{API-HERE}');

$resource = $api->getAlbums();

die(var_dump($resource));