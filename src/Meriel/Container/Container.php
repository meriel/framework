<?php namespace Meriel\Container;


class Container implements \ArrayAccess {

    private $container = array();

    protected $aliases = [
        'router' => '\Meriel\Routing\Router',
        'request' => '\Meriel\Http\Requests',
        'view' => '\Meriel\View\Views',
        'response' => '\Meriel\Http\Responses',
        'database' => '\Meriel\Database\Database',

    ];

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset) {
        return isset($this->container[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }


    public function register($index, $instance) {
        if (is_callable($instance)) {
            $instance = call_user_func($instance);
        }
        $this->container[$index] = $instance;
    }


    public function registerProviders(array $providers) {
        foreach ($providers as $key => $class) {
            $this->container[$key] = new $class;
        }
    }

    protected function resolve($abstract, $parameters = [])
    {
        if ($key = array_search($abstract, $this->aliases) && isset($this[$key])) {
            return $this[$key];
        }

        return new $abstract($parameters);
    }

    public function make($abstract, array $parameters = [])
    {
        return $this->resolve($abstract, $parameters);
    }

}
