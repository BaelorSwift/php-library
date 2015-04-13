<?php namespace Duffleman\baelor\Results;

use Illuminate\Support\Collection;

class CollectionSet {

    protected $set;
    protected $type;

    public function __construct($arraySet, $type = null)
    {
        $this->type = $type;
        $this->set = new Collection();
        if (isset($arraySet->result)) {
            foreach ($arraySet->result as $item) {
                $this->set[] = ResultParser::build($item);
            }
        } else {
            foreach ($arraySet as $item) {
                $this->set[] = ResultParser::build($item);
            }
        }
    }

    public function __get($variable)
    {
        if ($variable === $this->type) {
            return $this->set;
        }
    }
}