# baelorphp
  php library for [baelor.io](https://baelor.io).

## Contents
* **[Installation](#installation)**
* **[Introduction](#introduction)**
* **[Endpoints](#endpoints)**
  * [Create a user](#create-a-user)
  * [Login as existing user](#login-as-existing-user)
  * [Get an Album](#get-a-single-album)
  * [Get songs from an album](#songs-from-that-album)
  * [Get all songs](#all-songs)
  * [Get a single song](#get-a-single-song)
  * [Lyrics](#lyrics)
  * [Bae](#bae-status)
* **[Examples](#examples)**
  * [Line Length](#find-the-longest-line-in-a-song)
* **[Credits](#credits)**

## Installation
  $ `composer install --no-dev -o`

## Introduction
  baelorphp is a php library for the [baelor.io](https://baelor.io) Taylor Swift API.

  *To obtain an API key for baelor.io, run the [Create new API User](#create-a-user) example.*

#### Quick Example

  Let's load all of Taylor Swift's albums.

```php
use Duffleman\baelor\BaelorAPI;

$api = new BaelorAPI('api-key');
$albumCollection = $api->getAlbums();
```

You can also wrap it around try/catch tags to see what (if any) errors are thrown.

## Endpoints
  Here is what we can do, also see the [baelor.io docs](https://baelor.io/docs) for a full API endpoint list.

### Create a user

```php
use Duffleman\baelor\BaelorAPI;

$api = new BaelorAPI();
$user = $api->createUser('myUsername', 'myEmail', 'myPassword');

$ourNewAPIKey = $user->api_key;
```

### Login as existing user
```php
use Duffleman\baelor\BaelorAPI;

$api = new BaelorAPI();
$api->login('myUsername', 'myPassword');

$response = $api->getAlbums(); // Returns full set of Albums.
```

### Get a single album
```php
use Duffleman\baelor\BaelorAPI;

$api = new BaelorAPI('api-key');

$album = $api->getAlbums('1989');
```

### Songs from that Album
  Extending from the above example.
```php
use Duffleman\baelor\BaelorAPI;

$api = new BaelorAPI('api-key');

$album = $api->getAlbums('1989');

$songs = $album->attributes;
```

### All songs
```php
use Duffleman\baelor\BaelorAPI;

$api = new BaelorAPI('api-key');

$songCollection = $api->getSongs();
```

### Get a single song
```php
use Duffleman\baelor\BaelorAPI;

$api = new BaelorAPI('api-key');

$song = $api->getSongs('style');

$length = $song->length; // We can access attributes directly.
```

### Lyrics
  Lyrics works slightly differently. But equally as easy.

```php
use Duffleman\baelor\BaelorAPI;
use Duffleman\baelor\Results\Lyrics;

$api = new BaelorAPI('api-key');

$song = $api->getSongs('style');

$lyrics = new Lyrics($song, $api);

echo($lyrics->toHTML());
```

### Bae Status
```php
use Duffleman\baelor\BaelorAPI;

$api = new BaelorAPI('api-key');

$bae = $api->getBae('word');
// or
$bae = $api->getBae();

var_dump($bae);
```

## Examples

### Find the longest line in a song
```php
use Duffleman\baelor\BaelorAPI;
use Duffleman\baelor\Results\Lyrics;

$api = new BaelorAPI('api-key');

$song = $api->getSongs('style');
$lyrics = new Lyrics($song, $api);

$lines = $lyrics->toArray(true); // true because we do want to strip empty lines.

$longestLength = 0;
$longestLine = '';
foreach($lines as $line) {
  $lineLength = strlen($line);
  if($lineLength > $longestLength) {
    $longestLength = $lineLength;
    $longestLine = $line;
  }
}

echo("The longest line is {$lineLength} characters long. It reads: {$longestLine}.");
```

## Credits
  [Baelor API](http://baelor.io) created by [Alex Forbes-Reed](https://github.com/0xdeafcafe) [@0xdeafcafe](http://twitter.com/0xdeafcafe)

  [baelorjs libary](https://www.npmjs.com/package/baelorjs) created by [Jamie Davies](https://github.com/viralpickaxe) [@viralpickaxe](http://twitter.com/viralpickaxe)

  [baelorphp libary](https://packagist.org/packages/duffleman/baelorphp) created by [George Miller](https://github.com/duffleman) [@duffleman](http://twitter.com/duffleman)
