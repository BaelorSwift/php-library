<?php

require_once('vendor/autoload.php');

### TODO in order
# Lyrics
# - Lyric Functions
# Images
# TestSuite (PHPSpec/PHPUnit)
# Documentation
###

use Duffleman\baelor\BaelorAPI;

$api = new BaelorAPI();
$details = $api->login('bae@duffleman.co.uk', 'aReallySecretPassword');
