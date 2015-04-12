<?php namespace Duffleman\baelor;


class Result
{

    private $error = null;
    private $success = false;
    private $named;

    public function __construct($obj, $overrideName = 'attributes')
    {
        $this->named = $overrideName;
        $this->{$this->named} = [];
        $obj = json_decode($obj);
        $this->error = $obj->error;
        $this->success = $obj->success;
        foreach ($obj->result as $key => $value) {

            $this->{$this->named}[$key] = $value;
        }
    }

    public function setNamed($name)
    {
        $this->named = $name;
    }

    public function __get($var)
    {
        if (isset($this->$var)) {
            return $this->$var;
        }

        if (isset($this->{$this->named}[$var])) {
            return $this->{$this->named}[$var];
        }

        return null;
    }

}