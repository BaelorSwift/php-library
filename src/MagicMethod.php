<?php namespace Duffleman\baelor;

use Duffleman\baelor\Exceptions\InvalidBaeMethodNameException;

class MagicMethod {

    protected $catchers = [
        'get',
        'post',
        'patch',
        'delete'
    ];
    protected $methodType;
    protected $methodName;

    public function __construct($methodName)
    {
        return $this->build($methodName);
    }

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

    public function __toString()
    {
        return $this->methodName;
    }

    public function getType()
    {
        return $this->methodType;
    }

    public function getName()
    {
        return $this->methodName;
    }

    public function getClass()
    {
        $className = $this->getName();
        $className = rtrim($className, 's');
        $className = ucwords(strtolower($className));

        return $className;
    }
}