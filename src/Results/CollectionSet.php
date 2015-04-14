<?php namespace Duffleman\baelor\Results;

use Illuminate\Support\Collection;

/**
 * Custom collection set. Contains a collection of items.
 *
 * Class CollectionSet
 * @package Duffleman\baelor\Results
 */
class CollectionSet {

    /**
     * The collection itself.
     *
     * @var Collection
     */
    protected $set;
    /**
     * What type of set is inside this collection?
     *
     * @var null
     */
    protected $type;

    /**
     * @param      $arraySet
     * @param null $type
     */
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

    /**
     * Getter for the variables within the set.
     *
     * @param $variable
     * @return Collection
     */
    public function __get($variable)
    {
        if ($variable === $this->type) {
            return $this->set;
        }
    }
}