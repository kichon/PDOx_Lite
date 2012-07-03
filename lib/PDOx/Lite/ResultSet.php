<?php
namespace PDOx\Lite;

use PDOx\Lite\Row;

class ResultSet
{
    protected $prefix = "_";
    protected $wherePrefix = "W_";

    private $pdox_lite;
    private $table;
    private $sth;
    private $where = array();

    public function __construct($conf = array())
    {
        $this->pdox_lite = $conf['pdox_lite'];
        $this->table = $conf['table'];
    }

    public function all()
    {
        $sth = $this->select(array('*'));
        $sth->setFetchMode(\PDO::FETCH_ASSOC);
        return array_map(array($this, 'inflate_row'), $sth->fetchAll());
    }

    public function search($where = array())
    {
        $this->where = $where;
        return $this;
    }

    public function single()
    {
        $sth = $this->pdox_lite->dbh_do(array($this, "select_sth"));
        $sth->setFetchMode(\PDO::FETCH_ASSOC);
        $row = $sth->fetch();
        return $this->inflate_row($row);
    }

    public function next()
    {
        if (!isset($this->sth)) {
            $this->sth = $this->pdox_lite->dbh_do(array($this, "select_sth"));
        }
        $this->sth->setFetchMode(\PDO::FETCH_ASSOC);
        $row = $this->sth->fetch();

        if (!$row) return null;

        return $this->inflate_row($row);
    }

    public function count()
    {
        $sth = $this->pdox_lite->dbh_do(
            array($this, "select"),
            array("count(*)")
        );
        $sth->setFetchMode(\PDO::FETCH_NUM);
        $row = $sth->fetch();

        return $row[0];
    }

    public function get_column($column = array())
    {
        if (empty($column))
            throw new \Exception("get_column() requires a column name");

        $sth = $this->pdox_lite->dbh_do(
            array($this, "select"),
            $column
        );
        $sth->setFetchMode(\PDO::FETCH_ASSOC);
        return $sth->fetchAll();
    }

    public function find($param)
    {
        $where = array();

        $autopk = null;
        if (is_null($this->table->autopk))
            throw new \Exception("you must do autopk() before find.");
        $where[$this->table->autopk] = $param;
        return $this->search($where)->single();
    }

    public function select_sth()
    {
        $sql = sprintf("SELECT * FROM %s", $this->table->getName());
        $sql = $sql . $this->createWhere($this->where);
        $whereParams = $this->getWhereParams($this->where);
        $dbh = $this->pdox_lite->dbh;
        $sth = $dbh->prepare($sql);
        $sth->execute($whereParams);

        return $sth;
    }

    public function select($column = array())
    {
        $sql = sprintf("SELECT %s FROM %s", implode(',', $column), $this->table->getName());
        $sql = $sql . $this->createWhere($this->where);
        $whereParams = $this->getWhereParams($this->where);
        $dbh = $this->pdox_lite->dbh;
        $sth = $dbh->prepare($sql);
        $sth->execute($whereParams);

        return $sth;
    }

    public function insert($table, $values)
    {
        $sqlValues = array();
        foreach ($values as $k => $v) {
            $sqlValues[] = ":".$this->prefix.$k;
        }
        $sql = sprintf("INSERT INTO %s ( %s ) VALUES ( %s )", $table, implode(',', array_keys($values)), implode(',', $sqlValues));
        $bindValues = $this->getBindValues($values);

        //$dbh = $this->getConnection();
        $dbh = $this->pdox_lite->dbh;
        $sth = $dbh->prepare($sql);
        $sth->execute($bindValues);
    }

    public function update($table, $values, $where)
    {
        $sqlValues = array();
        foreach ($values as $k => $v) {
            $sqlValues[] = $k.'=:'.$this->prefix.$k;
        }
        $sql = sprintf("UPDATE %s SET %s", $table, implode(',', $sqlValues));
        $sql = $sql.$this->createWhere($where);
        $whereParams = $this->getWhereParams($where);
        $bindValues = $this->getBindValues($values);

        //$dbh = $this->getConnection();
        $dbh = $this->pdox_lite->dbh;
        $sth = $dbh->prepare($sql);
        $sth->execute(array_merge($bindValues, $whereParams));
    }

    public function delete($table, $where)
    {
        $sql = sprintf("DELETE FROM %s", $table);
        $sql = $sql.$this->createWhere($where);

        $whereParams = $this->getWhereParams($where);

        //$dbh = $this->getConnection();
        $dbh = $this->pdox_lite->dbh;
        $sth = $dbh->prepare($sql);
        $sth->execute($whereParams);
    }

    private function createWhere($where)
    {
        if (empty($where)) return null;

        $whereSql = array();
        foreach ($where as $k => $v) {
            $whereSql[] = $k.' = :'.$this->wherePrefix.$k;
        }

        return " WHERE ".join(" AND ", $whereSql);
    }

    private function getWhereParams($values)
    {
        if (empty($values)) return null;

        $bindValues = array();
        foreach ($values as $k => $v) {
            $bindValues[":".$this->wherePrefix.$k] = $v;
        }

        return $bindValues;
    }

    private function getBindValues($values)
    {
        if (empty($values)) return null;

        $bindValues = array();
        foreach ($values as $k => $v) {
            $bindValues[":".$this->prefix.$k] = $v;
        }

        return $bindValues;
    }

    public function inflate_row($row) {
        $package = new Row(
            array(
                'pdox_lite' => $this->pdox_lite,
                'table'     => $this->table,
                'data'      => $row,
            )
        );

        return $package;
    }
}
