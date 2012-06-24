<?php

namespace PDOx;

class Lite
{
    protected $default = array(
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'dbname'    => 'test',
            'root'      => 'root',
            'password'  => ''
        );

    protected $config;
    protected $connections = array();
    protected $prefix = "_";
    protected $wherePrefix = "W_";

    public function connect($name, $config = array())
    {
        $this->config = array_merge(
                $this->default,
                $config
            );

        $dsn = sprintf('%s:host=%s;dbname=%s', $this->config['driver'], $this->config['host'], $this->config['dbname']);
        $dbh = new \PDO($dsn, $this->config['user'], $this->config['password']);
        $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->connections[$name] = $dbh;

        return true;
    }

    public function getConnection($name = null)
    {
        if (is_null($name)) {
            return current($this->connections);
        }

        return $this->connections[$name];
    }

    public function exec($sql)
    {
        $dbh = $this->getConnection();
        $dbh->exec($sql);
    }
    
    public function select($table, $column, $where)
    {
        $sql = sprintf("SELECT %s FROM %s", implode(',', $column), $table);
        $sql = $sql . $this->createWhere($where);
        $whereParams = $this->getWhereParams($where);

        $dbh = $this->getConnection();
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

        $dbh = $this->getConnection();
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

        $dbh = $this->getConnection();
        $sth = $dbh->prepare($sql);
        $sth->execute(array_merge($bindValues, $whereParams));
    }

    public function delete($table, $where)
    {
        $sql = sprintf("DELETE FROM %s", $table);
        $sql = $sql.$this->createWhere($where);

        $whereParams = $this->getWhereParams($where);

        $dbh = $this->getConnection();
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
}
