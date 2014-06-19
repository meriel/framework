<?php


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

        $pattern = "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', preg_quote($this->uri)) . "$@D";
        $matches = array();

        if (Request::method() == $this->methods && preg_match($pattern, Request::path(), $matches)) {
            
            array_shift($matches);
            
            $this->parameters = $matches;
            
            return true;
        }

        return false;
    }
    
    
    public function parameters(){
        return $this->parameters;
    }

    public function run() {
        
        $parameters = array_filter($this->parameters(), function($p) {
            return isset($p);
        });
        

        return call_user_func_array($this->action['uses'], $parameters);
    }

    protected function parseAction($action) {
        if (is_callable($action)) {

            return array('uses' => $action);
        } elseif (!isset($action['uses'])) {

            $action['uses'] = $this->findClosure($action);
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

        return $this->value($default);
    }

    private function value($value) {
        return $value instanceof Closure ? $value() : $value;
    }

}
