<?php namespace Duffleman\baelor\Results;

/**
 * Represents an album
 *
 * Class Album
 * @package Duffleman\baelor\Results
 */
class Album extends Generic {

    /**
     * Constructor
     *
     * @param      $resultSet
     * @param bool $loadSongs
     */
    public function __construct($resultSet, $loadSongs = true)
    {
        parent::__construct($resultSet);

        if ($loadSongs) {
            $this->buildSongLibrary();
        }
    }

    /**
     * Builds the internal song library.
     */
    private function buildSongLibrary()
    {
        $songLibrary = new CollectionSet($this->songs, 'songs');
        $this->songs = $songLibrary->songs;
    }

} 