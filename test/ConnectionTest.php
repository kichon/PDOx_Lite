<?php

require_once 'PHPUnit/Autoload.php';
require_once dirname(__DIR__).'/lib/PDOx/Autoloader.php';

use PDOx\Autoloader,
    PDOx\Lite;

class ConnectionTest extends PHPUnit_Framework_TestCase
{
    private $db;

    protected function setUp()
    {
        Autoloader::register();
        $this->db = new Lite();
    }

    public function testConnection()
    {
        $this->assertTrue(
            $this->db->connect(
                'master',
                array(
                    'host'      => 'localhost',
                    'dbname'    => 'pdox',
                    'user'      => 'root',
                    'password'  => ''
                )
            )
        );
    }
}
