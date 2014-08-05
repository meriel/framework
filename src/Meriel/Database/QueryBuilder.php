<?php

namespace Meriel\Database;

use Closure;
use Meriel\Database\Database as Database;

class QueryBuilder {

    protected $table = '';
    public $columns;
    protected $connection;
    public $from;
    public $wheres;
    public $aggregate;
    public $distinct = false;
    public $joins;
    public $groups;
    public $havings;
    public $orders;
    public $limit;
    public $offset;
    public $unions;
    public $lock;
    protected $bindings = array(
        'select' => array(),
        'join' => array(),
        'where' => array(),
        'having' => array(),
        'order' => array(),
    );
    protected $selectComponents = array(
        'aggregate',
        'columns',
        'from',
        'joins',
        'wheres',
        'groups',
        'havings',
        'orders',
        'limit',
        'offset',
        'unions',
        'lock',
    );

    function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public function newQuery() {
        return new QueryBuilder($this->connection);
    }



    function __clone() {
        $this->connection = clone $this->connection;
    }

    public function update(array $values) {
        $bindings = array_values(array_merge($values, $this->getBindings()));

        $sql = $this->compileUpdate($values);      

        return $this->connection->update($sql, $this->cleanBindings($bindings));
    }

    function insert($values) {


        if (!is_array(reset($values))) {
            $values = array($values);
        } else {
            foreach ($values as $key => $value) {
                ksort($value);
                $values[$key] = $value;
            }
        }


        $bindings = array();

        foreach ($values as $record) {
            foreach ($record as $value) {
                $bindings[] = $value;
            }
        }

        $sql = $this->compileInsert($values);



        $bindings = $this->cleanBindings($bindings);

        return $this->connection->insert($sql, $bindings);
    }

    protected function cleanBindings(array $bindings) {
        return array_values(array_filter($bindings, function($binding) {
                    return $binding;
                }));
    }

    public function getBindings() {

        $return = array();

        array_walk_recursive($this->bindings, function($x) use (&$return) {
            $return[] = $x;
        });
        return $return;
    }

    public function from($table) {
        $this->from = $table;

        return $this;
    }

    public function select($columns = array('*')) {
        $this->columns = is_array($columns) ? $columns : func_get_args();

        return $this;
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and') {

        if (is_array($column)) {
            return $this->_whereNested(function($query) use ($column) {
                        foreach ($column as $key => $value) {
                            $query->where($key, '=', $value);
                        }
                    }, $boolean);
        }

        if (func_num_args() == 2) {
            list($value, $operator) = array($operator, '=');
        }

        $type = 'Basic';

        $this->wheres[] = compact('type', 'column', 'operator', 'value', 'boolean');


        $this->addBinding($value, 'where');



        return $this;
    }

    public function _whereNested(Closure $callback, $boolean = 'and') {

        $query = $this->newQuery();

        $query->from($this->from);

        call_user_func($callback, $query);

        return $this->addNestedWhereQuery($query, $boolean);
    }

    public function addNestedWhereQuery($query, $boolean = 'and') {
        if (count($query->wheres)) {
            $type = 'Nested';

            $this->wheres[] = compact('type', 'query', 'boolean');

            $this->mergeBindings($query);
        }

        return $this;
    }

    public function mergeBindings(QueryBuilder $query) {
        $this->bindings = array_merge_recursive($this->bindings, $query->bindings);

        return $this;
    }

    public function whereIn($column, $values, $boolean = 'and', $not = false) {
        $type = $not ? 'NotIn' : 'In';

        $this->wheres[] = compact('type', 'column', 'values', 'boolean');

        $this->addBinding($values, 'where');

        return $this;
    }

    private function toSql() {
        return $this->compileSelect();
    }

    public function addBinding($value, $type = 'where') {
        if (!array_key_exists($type, $this->bindings)) {
            throw new \Exception("Invalid binding type: {$type}.");
        }

        if (is_array($value)) {
            $this->bindings[$type] = array_values(array_merge($this->bindings[$type], $value));
        } else {
            $this->bindings[$type][] = $value;
        }

        return $this;
    }



    protected function concatenate($segments) {
        return implode(' ', array_filter($segments, function($value) {
                    return (string) $value !== '';
                }));
    }

    protected function compileComponents() {
        $sql = array();

        foreach ($this->selectComponents as $component) {
            // To compile the query, we'll spin through each component of the query and
            // see if that component exists. If it does we'll just call the compiler
            // function for the component which is responsible for making the SQL.
            if (!is_null($this->$component)) {
                $method = 'compile' . ucfirst($component);

                $sql[$component] = $this->$method($this->$component);
            }
        }

        return $sql;
    }

    private function compileSelect() {
        if (is_null($this->columns))
            $this->columns = array('*');

        return trim($this->concatenate($this->compileComponents()));
    }

    protected function compileWheres() {
        $sql = array();

        if (is_null($this->wheres))
            return '';


        foreach ($this->wheres as $where) {
            $method = "where{$where['type']}";

            $sql[] = $where['boolean'] . ' ' . $this->$method($where);
        }

        if (count($sql) > 0) {
            $sql = implode(' ', $sql);

            return 'where ' . preg_replace('/and |or /', '', $sql, 1);
        }

        return '';
    }

    protected function compileColumns($columns) {

        if (!is_null($this->aggregate))
            return;

        $select = $this->distinct ? 'select distinct ' : 'select ';

        return $select . $this->columnize($columns);
    }

    protected function whereBasic($where) {

        $sql = "`" . $where['column'] . "` " . $where['operator'];

        if (is_numeric($where['value'])) {
            $sql .= " " . $where['value'];
        } else {
            $sql .= " '" . $where['value'] . "'";
        }

        return $sql;
    }

    protected function whereNested($where) {
        $nested = $where['query'];

        return '(' . substr($this->compileWheres($nested), 6) . ')';
    }

    public function aggregate($function, $columns = array('*')) {
        $this->aggregate = compact('function', 'columns');

        $previousColumns = $this->columns;

        $results = $this->get($columns);


        $this->aggregate = null;

        $this->columns = $previousColumns;

        if (isset($results[0])) {
            $result = array_change_key_case((array) $results[0]);

            return $result['aggregate'];
        }
    }

    public function get($columns = array('*')) {
        if (is_null($this->columns))
            $this->columns = $columns;

        return $this->connection->select($this->toSql(), $this->getBindings());
    }

    public function columnize(array $columns) {
        return implode(', ', $columns);
    }

    protected function compileFrom($table) {
        return 'from ' . $table;
    }

    public function compileUpdate($values) {
        $table = $this->from;


        $columns = array();

        foreach ($values as $key => $value) {
            $columns[] = $key . ' = ' . $this->parameter($value);
        }

        $columns = implode(', ', $columns);


        if (isset($this->joins)) {
            $joins = ' ' . $this->compileJoins($this->joins);
        } else {
            $joins = '';
        }


        $where = $this->compileWheres();

        return trim("update {$table}{$joins} set $columns $where");
    }

    protected function compileJoins($joins) {
        
    }

    public function compileInsert(array $values) {


        $table = $this->from;

        if (!is_array(reset($values))) {
            $values = array($values);
        }

        $columns = $this->columnize(array_keys(reset($values)));


        $parameters = $this->parameterize(reset($values));

        $value = array_fill(0, count($values), "($parameters)");

        $parameters = implode(', ', $value);

        return "insert into $table ($columns) values $parameters";
    }

    public function parameterize(array $values) {
        return implode(', ', array_map(array($this, 'parameter'), $values));
    }

    public function parameter($value) {
        if (is_numeric($value)) {
            return $value;
        } else {
            return "'$value'";
        }
    }

    public function getConnection() {
        return $this->connection;
    }

}
