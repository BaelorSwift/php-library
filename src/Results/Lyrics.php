<?php namespace Duffleman\baelor\Results;

use Duffleman\baelor\BaelorAPI;
use Illuminate\Support\Collection;

class Lyrics {

    private $song;
    private $bae;
    private $raw;

    public function __construct(Song $song, BaelorAPI $bae)
    {
        $this->song = $song;
        $this->bae = $bae;

        $this->grab();
    }

    public function grab()
    {
        $songSlug = $this->song->slug;
        $url = 'songs/' . $songSlug . '/lyrics';
        $request = $this->bae->prepareRequest('get', $url);
        $response = $this->bae->process($request);

        $this->raw = $response->lyrics;
    }

    public function toHTML()
    {
        $lines = explode("\n", $this->raw);
        $lines = new Collection($lines);

        $lines = $lines->map(function ($item) {
            if (empty($item)) {
                return '</p><p>';
            }

            return $item . '<br>';
        });

        $lines->prepend('<p>');
        $lines->push('</p>');

        return implode($lines->toArray());
    }

    public function toBR()
    {
        return str_replace("\n", "<br>", $this->raw);
    }

    public function raw()
    {
        return $this->raw;
    }

    public function toArray($stripBlanks = false)
    {
        $lines = explode("\n", $this->raw);

        if ( !$stripBlanks) {
            return $lines;
        }

        $lines = array_filter($lines);

        return $lines;
    }
} 