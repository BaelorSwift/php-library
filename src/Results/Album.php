<?php namespace Duffleman\baelor\Results;

class Album extends Generic {

    public function __construct($resultSet, $loadSongs = true)
    {
        parent::__construct($resultSet);

        if ($loadSongs) {
            $this->buildSongLibrary();
        }
    }

    private function buildSongLibrary()
    {
        $songLibrary = new CollectionSet($this->songs, 'songs');
        $this->songs = $songLibrary->songs;
    }

    public function toArray()
    {
        return $this->attributes;
    }
} 