<?php namespace Duffleman\baelor\Results;

use Duffleman\baelor\Exceptions\MissingBaeriableException;

/**
 * Base class for all item types.
 *
 * Class Generic
 * @package Duffleman\baelor\Results
 */
class Generic {

    /**
     * The set of attributes that make up this item.
     *
     * @var
     */
    protected $attributes;

    /**
     * Feed it a request from the API, it'll compose the items.
     *
     * @param $resultSet
     */
    public function __construct($resultSet)
    {
        if (isset($resultSet->result)) {
            foreach ($resultSet->result as $key => $value) {
                $this->attributes[$key] = $value;
            }
        } else {
            foreach ($resultSet as $key => $value) {
                $this->attributes[$key] = $value;
            }
        }
    }

    /**
     * Getter for the attribute set.
     *
     * @param $variable
     * @throws MissingBaeriableException
     */
    public function __get($variable)
    {
        if (isset($this->$variable)) {
            return $this->$variable;
        }

        if (isset($this->attributes[$variable])) {
            return $this->attributes[$variable];
        }

        throw new MissingBaeriableException('Cannot find `' . $variable . '` in this class.');
    }

    /**
     * Returns the attribute set as an array.
     *
     * @return mixed
     */
    public function toArray()
    {
        return $this->attributes;
    }
}