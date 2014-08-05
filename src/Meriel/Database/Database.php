<?php

namespace Meriel\Database;

use Config;
use PDO;
use Meriel\Database\Connection;

class Database {

    protected $connections = array();
    private $error;

    protected $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    );

    private function __clone() {
        
    }

    function __construct() {}

    private function getDefaultConnection() {
        $config = Config::get('database');
        return $config['default'];
    }
    
    
    public function table($table){
        $conn = $this->connection();
        
        return $conn->table($table);
    }

    public function connection($name = null) {
        $name = $name ? : $this->getDefaultConnection();



        if (!isset($this->connections[$name])) {

            $this->connections[$name] = $this->makeConnection($name);
        }


        return $this->connections[$name];
    }

    private function makeConnection($name) {

        $config = $this->getConfig($name);

        try {
            $connection = new PDO($config['driver'] . ':host=' .
                    $config['host'] . ';dbname=' . $config['database'], $config['username'], $config['password'], $this->options);
        }
        
        catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }

        return new Connection($connection);
    }

    private function getConfig($name) {

        $config = Config::get('database');

        return $config['connection'][$name];
    }

    public function __call($method, $parameters) {       // var_dump($method, $parameters);
        return call_user_func_array(array($this->connection(), $method), $parameters);
    }


}
