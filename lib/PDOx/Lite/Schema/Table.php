<?php
namespace PDOx\Lite\Schema;

class Table
{
    private $name = null;

    public function __construct($conf = array())
    {
        $this->name = $conf['name'];
    }

    public function getName()
    {
        return $this->name;
    }
}
