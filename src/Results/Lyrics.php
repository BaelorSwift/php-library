<?php namespace Duffleman\baelor\Results;

use Duffleman\baelor\BaelorAPI;
use Duffleman\baelor\Exceptions\MissingBaeriableException;
use Illuminate\Support\Collection;

/**
 * Handles the lyrics end point.
 *
 * Class Lyrics
 * @package Duffleman\baelor\Results
 */
class Lyrics {

    /**
     * The song that we want lyrics for.
     *
     * @var \Duffleman\baelor\Results\Song
     */
    private $song;
    /**
     * An API object to make calls with.
     *
     * @var \Duffleman\baelor\BaelorAPI
     */
    private $bae;
    /**
     * The raw return of the lyrics string.
     *
     * @var
     */
    private $raw;

    /**
     * @param \Duffleman\baelor\Results\Song $song
     * @param \Duffleman\baelor\BaelorAPI    $bae
     */
    public function __construct($song, BaelorAPI $bae)
    {
        $this->song = $song;
        $this->bae = $bae;

        $this->validateSong();

        $this->grab();
    }

    /**
     * Grabs the raw lyrics and saves them in the class.
     *
     * @throws \Duffleman\baelor\Exceptions\InvalidBaePIException
     * @throws \Duffleman\baelor\Exceptions\UnauthorizedBaeException
     */
    public function grab()
    {
        $songSlug = $this->song->slug;
        $url = 'songs/' . $songSlug . '/lyrics';
        $request = $this->bae->prepareRequest('get', $url);
        $response = $this->bae->process($request);

        $this->raw = $response->lyrics;
    }

    /**
     * Returns the lyrics as HTML. Within paragraph tags and break lines.
     *
     * @return string
     */
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

    /**
     * Returns the lyrics with break lines.
     * Includes empty lines.
     *
     * @return mixed
     */
    public function toBR()
    {
        return str_replace("\n", "<br>", $this->raw);
    }

    /**
     * Returns the raw string.
     *
     * @return mixed
     */
    public function raw()
    {
        return $this->raw;
    }

    /**
     * Returns the lyrics as an array.
     * Each line is a new element.
     * Pass `stripBlanks` to include empty array elements.
     *
     * @param bool $stripBlanks
     * @return array
     */
    public function toArray($stripBlanks = true)
    {
        $lines = explode("\n", $this->raw);

        if ( !$stripBlanks) {
            return $lines;
        }

        $lines = array_filter($lines);

        return $lines;
    }

    private function validateSong()
    {
        if(!isset($this->song->slug)) {
            throw new MissingBaeriableException('This song needs a slug!');
        }
    }
} 