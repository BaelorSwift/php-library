<?php namespace Duffleman\baelor;

use Duffleman\baelor\Exceptions\InvalidBaeMethodNameException;

/**
 * Disects magic method calls.
 *
 * Class MagicMethod
 * @package Duffleman\baelor
 */
class MagicMethod {

    /**
     * Catch these key words at the beginning.
     *
     * @var array
     */
    protected $catchers = [
        'get',
        'post',
        'patch',
        'delete'
    ];
    /**
     * Holds the method type.
     *
     * @var
     */
    protected $methodType;
    /**
     * Holds the method name.
     *
     * @var
     */
    protected $methodName;

    /**
     * Constructor.
     *
     * @param $methodName
     * @throws \Duffleman\baelor\Exceptions\InvalidBaeMethodNameException
     */
    public function __construct($methodName)
    {
        return $this->build($methodName);
    }

    /**
     * Seperates the method type and name.
     *
     * @param $methodName
     * @return array
     * @throws \Duffleman\baelor\Exceptions\InvalidBaeMethodNameException
     */
    private function build($methodName)
    {
        $foundType = false;
        foreach ($this->catchers as $catcher) {
            if (strpos($methodName, $catcher) === 0) {
                $foundType = true;
                $this->methodType = $catcher;
                continue;
            }
        }

        if ( !$foundType) {
            throw new InvalidBaeMethodNameException($methodName . ' is not a valid method name.');
        }

        if (substr($methodName, 0, strlen($this->methodType)) == $this->methodType) {
            $methodName = substr($methodName, strlen($this->methodType));
        }

        $this->methodName = strtolower($methodName);

        return ['type' => $this->methodType, 'name' => $this->methodName];
    }

    /**
     * Can be given as string.
     *
     * @return mixed
     */
    public function __toString()
    {
        return $this->methodName;
    }

    /**
     * Returns the type. (get, post, patch, etc)
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->methodType;
    }

    /**
     * Returns the name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->methodName;
    }

    /**
     * Returns the Class the result can be represented as.
     * Call $api->getAlbums(); would be 'Album';
     *
     * @return mixed|string
     */
    public function getClass()
    {
        $className = $this->getName();
        $className = rtrim($className, 's');
        $className = ucwords(strtolower($className));

        return $className;
    }
}