<?php
namespace PDOx\Lite\Schema;

class Table
{
    private $name = null;
    private $autopk;
    private $pk = array();

    public function __construct($conf = array())
    {
        $this->name = $conf['name'];
    }

    public function getName()
    {
        return $this->name;
    }

    public function autopk($val)
    {
        if ($val) {
            $this->autopk = $val;
            $this->pk[] = $val;
            return $this;
        }
        return $this->autopk;
    }

    public function __get($name)
    {
        return isset($this->$name) ? $this->$name : null;
    }
}
