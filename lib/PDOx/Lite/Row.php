<?php
namespace PDOx\Lite;

class Row
{
    private $pdox_lite = null;
    private $table = null;
    private $data = array();

    public function __construct($arr = array())
    {
        foreach (array('pdox_lite', 'table', 'data') as $column) {
            if (!isset($arr[$column]))
                throw new \InvalidArgumentException("you are need $column");
            
            $this->$column = $arr[$column];
        }
    }

    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }
}
