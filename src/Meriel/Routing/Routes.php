<?php

namespace Meriel\Routing;

use Request;

class Routes {

    protected $uri;
    protected $methods;
    protected $action;
    protected $parameters = array();

    public function __construct($method, $uri, $action) {
        $this->uri = $uri;
        $this->methods = $method;
        $this->action = $this->parseAction($action);
    }

    public function matches() {

        $uri = $this->getCurrentUri();

        $matches = array();

        //if (Request::method() == $this->methods && preg_match($pattern, Request::path(), $matches)) {
        if (Request::method() == $this->methods && preg_match_all('#^' . $this->uri . '$#', $uri, $matches, PREG_OFFSET_CAPTURE)) {

            $matches = array_slice($matches, 1);

            $this->parameters = array_map(function($match, $index) use ($matches) {
                
                if (isset($matches[$index + 1]) && isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
                    return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
                }
                
                else {
                    return (isset($match[0][0]) ? trim($match[0][0], '/') : null);
                }
            }, $matches, array_keys($matches));

            return true;
        }

        return false;
    }

    public function parameters() {
        return $this->parameters;
    }

    public function run() {
        
        $parameters = array_filter($this->parameters(), function($p) {
            return isset($p);
        });


        return call_user_func_array($this->action['fn'], $parameters);
    }

    protected function parseAction($action) {
        if (is_callable($action)) {

            return array('fn' => $action);
        } elseif (!isset($action['fn'])) {

            $action['fn'] = $this->findClosure($action);
        }

        return $action;
    }

    protected function findClosure(array $action) {

        return $this->array_first($action, function($key, $value) {

                    return is_callable($value);
                });
    }

    private function array_first($array, $callback, $default = null) {
        foreach ($array as $key => $value) {
            if (call_user_func($callback, $key, $value))
                return $value;
        }

        return $default instanceof Closure ? $default() : $default;
    }

    private function getCurrentUri() {

        $basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        $uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));

        if (strstr($uri, '?'))
            $uri = substr($uri, 0, strpos($uri, '?'));

        $uri = '/' . trim($uri, '/');
        return $uri;
    }

}
