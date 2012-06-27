<?php
namespace PDOx\Lite;

use PDOx\Lite\Row;

class ResultSet
{
    private $pdox_lite;
    private $table;

    public function __construct($conf = array())
    {
        $this->pdox_lite = $conf['pdox_lite'];
        $this->table = $conf['table'];
    }

    public function all()
    {
        $stmt = $this->select(array('*'));
        return array_map(array($this, 'inflate_row'), $stmt->fetchAll());
    }

    public function select($column = array(), $where = array())
    {
        $sql = sprintf("SELECT %s FROM %s", implode(',', $column), $this->table->getName());
        $sql = $sql . $this->createWhere($where);
        $whereParams = $this->getWhereParams($where);

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
