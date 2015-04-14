<?php namespace Duffleman\baelor\Results;

/**
 * Represents a single song.
 *
 * Class Song
 * @package Duffleman\baelor\Results
 */
class Song extends Generic {

    /**
     * Build the song, find it's album, if needed.
     *
     * @param $dataSet
     */
    public function __construct($dataSet)
    {
        parent::__construct($dataSet);

        $this->findAlbum();
    }

    /**
     * Finds the album and converts it into an Album object.
     */
    private function findAlbum()
    {
        if (isset($this->attributes['album'])) {
            $this->album = new Album($this->attributes['album'], false);
        }
    }
}