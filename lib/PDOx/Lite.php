<?php
namespace PDOx;

use PDOx\Lite\Schema,
    PDOx\Lite\ResultSet;

class Lite
{
    public $dbh = null;

    private $schema = null;
    private $abstract = null;
    private $connector = null;

    private static $class = null;


    public function __construct($config = array())
    {
        if (is_null($this->schema))
            $this->schema = new Schema();

        try {
            if (!($this->schema instanceof Schema))
                throw new \Exception('schema must be a PDOx::Lite::Schema object');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            exit();
        }

        //コネクション管理
        $name = isset($config['name']) ? $config['name'] : 'master';
        $this->dbh = new \PDO($config['dsn'], $config['username'], $config['password']);
        $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->connector[$name] = $this->dbh;
    }

    public static function connect($config = array())
    {
        if (self::$class != null)
            return self::$class;

        self::$class = new Lite($config);

        return self::$class;
    }

    public function table($table_name)
    {
        $table = $this->schema->table($table_name);
        $package = new ResultSet(
            array(
                'pdox_lite' => $this,
                'table'     => $table
            )
        );

        return $package;
    }

    public function dbh_do($callback = array()) {
        $sth = call_user_func($callback);
        $sth->setFetchMode(\PDO::FETCH_ASSOC);
        return $sth->fetch();
    }
}
