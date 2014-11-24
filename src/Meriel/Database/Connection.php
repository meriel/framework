<?php namespace Meriel\Database;


/*
 * This file is part of the Meriel framework.
 *
 * (c) Stefano Anedda <dearste@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


use PDO;
use Closure;
use DateTime;
use Meriel\Database\QueryBuilder;

class Connection {

    protected $pdo;
    protected $fetchMode = PDO::FETCH_ASSOC;
    protected $database;
    protected $tablePrefix = '';
    protected $config = array();

    public function __construct(PDO $pdo, $database = '', $tablePrefix = '', array $config = array()) {
        
        $this->pdo = $pdo;
        $this->database = $database;
        $this->tablePrefix = $tablePrefix;
        $this->config = $config;
    }

    public function table($table) {


        $query = new QueryBuilder($this);

        return $query->from($table);
    }

    public function select($query, $bindings = array()) {

        return $this->run($query, $bindings, function($self, $query, $bindings) {

                    $statement = $self->getPdo()->prepare($query);

                    $statement->execute($self->prepareBindings($bindings));

                    return $statement->fetchAll($self->getFetchMode());
                });
    }

    public function insert($query, $bindings = array()) {
        return $this->statement($query, $bindings);
    }

    public function update($query, $bindings = array()) {
        return $this->affectingStatement($query, $bindings);
    }

    public function getFetchMode() {
        return $this->fetchMode;
    }

    public function prepareBindings(array $bindings) {


        foreach ($bindings as $key => $value) {
            if ($value === false) {
                $bindings[$key] = 0;
            }
        }

        return $bindings;
    }

    public function getPdo() {
        return $this->pdo;
    }

    public function statement($query, $bindings = array()) {
        return $this->run($query, $bindings, function($self, $query, $bindings) {
                    // if ($self->pretending())
                    //    return true;

                    $bindings = $self->prepareBindings($bindings);

                    return $self->getPdo()->prepare($query)->execute($bindings);
                });
    }

    public function affectingStatement($query, $bindings = array()) {
        return $this->run($query, $bindings, function($self, $query, $bindings) {
                
                //if ($me->pretending()) return 0;
                   
                $statement = $self->getPdo()->prepare($query);                    
               
                //var_dump($self->prepareBindings($bindings));

                $statement->execute($self->prepareBindings($bindings));

                return $statement->rowCount();
        });
    }

    protected function run($query, $bindings, Closure $callback) {
        try {

            $result = $callback($this, $query, $bindings);
        } catch (\Exception $e) {

            throw new \Exception($e);
        }

        return $result;
    }

}
