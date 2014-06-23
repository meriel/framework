<?php namespace Meriel\Database;

use Config;
use PDO;

class Database {

    protected $db = null;
    private $error;
    private $table;
    private $where = array();
    private $limit;
    private $bindings = array();

    private function __clone() {
        
    }

    function __construct() {
        
        $config = Config::get('database');

        $options = array(
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
            PDO::ATTR_PERSISTENT => true
        );
        try {
            $this->db = new PDO($config['connection']['driver'] . ':host=' . 
                    $config['connection']['host'] . ';dbname=' . $config['connection']['database'], 
                    $config['connection']['username'], $config['connection']['password'], $options);
        }
        // Catch any errors
        catch (PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

    function getPdo() {
        return $this->db;
    }

    /* public function select($query, $params = array()) {
      try {
      $stmt = $this->db->prepare($query);
      $stmt->execute($params);
      return $stmt->fetchAll();
      } catch (PDOException $e) {
      throw new Exception($e->getMessage());
      }
      } */

    public function table($table) {
        $this->table = $table;

        return $this;
    }

    public function where($key, $oper, $value) {
        $this->where[] = "AND " . $key . ' ' . $oper . ' ' . "?";
        $this->bindings[] = $value;
        
        return $this;
    }

    public function limit($limit = 10) {
        $this->limit = $limit;
        
        return $this;
    }

    public function get($columns = "*", $fetch_mode = PDO::FETCH_ASSOC, $class_name = '') {

        $sql = "SELECT " . $columns . " FROM " . $this->table .
                $this->getWhere() .
                $this->getLimit();

        $sth = $this->db->prepare($sql);

        if ($fetch_mode == PDO::FETCH_CLASS)
            $sth->setFetchMode($fetch_mode, $class_name);
        else
            $sth->setFetchMode($fetch_mode);

        for ($i = 1; $i <= count($this->bindings); $i ++) {
            $sth->bindParam($i, $this->bindings[$i - 1]);
        }

        $sth->execute();

        return $sth->fetchAll();
    }

    public function first($columns = '*', $fetch_mode = PDO::FETCH_ASSOC, $class_name = '') {
        return array_shift(
                $this->get($columns, $fetch_mode, $class_name)
        );
    }

    public function save($attributes = array(), $id_name = "id") {
        $attributes = $this->matchColumns($attributes);

        if (isset($attributes[$id_name]))
            $sql = $this->getUpdate($attributes, $id_name);
        else
            $sql = $this->getInsert($attributes);

        $sth = $this->db->prepare($sql);

        $sth->execute($attributes);
    }

    private function getInsert($attributes = array()) {
        $keys = array_keys($attributes);

        return "INSERT INTO " . $this->table .
                "(" . implode(",", $keys) . ")" .
                " VALUES(:" . implode(",:", $keys) . ")";
    }

    private function getUpdate($attributes, $id_name) {
        $updates = '';
        foreach ($attributes as $key => $value) {
            $updates .= $key . '=:' . $key . ',';
        }
        $updates = rtrim($updates, ",");

        return "UPDATE " . $this->table .
                " SET " . $updates .
                " WHERE " . $id_name . "=:" . $id_name;
    }

    private function getWhere() {
        if (empty($this->where))
            return '';

        $where_sql = ' WHERE ' . ltrim(implode(" ", $this->where), "ANDOR");

        return $where_sql;
    }

    private function getLimit() {
        if (isset($limit))
            return " Limit " . $limit;
    }

    private function matchColumns($attributes) {
        $new = array();

        $statement = $this->db->query('DESCRIBE ' . $this->table);

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (isset($attributes[$row['Field']]))
                $new[$row['Field']] = $attributes[$row['Field']];
        }

        return $new;
    }

}
