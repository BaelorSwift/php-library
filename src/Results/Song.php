<?php namespace Duffleman\baelor\Results;

class Song extends Generic {

    public function __construct($dataSet)
    {
        parent::__construct($dataSet);

        $this->findAlbum();
    }

    private function findAlbum()
    {
        if (isset($this->attributes['album'])) {
            $this->album = new Album($this->attributes['album'], false);
        }
    }

    public function toArray()
    {
        return $this->attributes;
    }
}