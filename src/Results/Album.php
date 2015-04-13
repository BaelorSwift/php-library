<?php namespace Duffleman\baelor\Results;

class Album extends Generic {

    public function __construct($resultSet)
    {
        parent::__construct($resultSet);

        $this->buildSongLibrary();
    }

    private function buildSongLibrary()
    {
        $songLibrary = new CollectionSet($this->songs, 'songs');
        $this->songs = $songLibrary->songs;
    }
} 