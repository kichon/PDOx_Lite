<?php
namespace PDOx\Lite;

use PDOx\Lite\Schema\Table;

class Schema
{
    private $tables = array();

    public function __construct()
    {
        //var_dump("PDOx::Lite::Schema");
    }

    public function table($table_name)
    {
        if (!isset($this->tables[$table_name]))
            $this->tables[$table_name] = new Table(array('name' => $table_name));

        return $this->tables[$table_name];
    }
}
