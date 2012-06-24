<?php
namespace PDOx;

require_once __DIR__.'/Autoloader.php';
use PDOx\Autoloader,
    PDOx\Lite\Schema;

class Lite
{
    private static $class = null;

    private $schema = null;
    private $abstract = null;
    private $connector = null;
    private $dbh = null;

    public function __construct($config = array())
    {
        Autoloader::register();

        if (is_null($this->schema))
            $this->schema = new Schema();

        if (!($this->schema instanceof PDOx\Lite\Schema))
            throw new Exception('schema must be a PDOx::Lite::Schema object');
    }

    public static function connect($config = array())
    {
        if (self::$class != null)
            return self::$class;

        self::$class = new Lite($config);

    }
}
