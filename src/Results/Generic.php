<?php namespace Duffleman\baelor\Results;

use Duffleman\baelor\Exceptions\MissingBaeriableException;

class Generic {

    protected $attributes;

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
}