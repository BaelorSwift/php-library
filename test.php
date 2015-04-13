<?php

require_once('vendor/autoload.php');

### TODO in order
# Images(?)
# TestSuite (PHPSpec/PHPUnit)
# Documentation
###

use Duffleman\baelor\BaelorAPI;
use Duffleman\baelor\Results\Lyrics;

$api = new BaelorAPI('{API-HERE}');

$song = $api->getSongs('shake-it-off');
$lyrics = new Lyrics($song, $api);

echo($lyrics->toHTML());