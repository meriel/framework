<?php namespace Meriel\Container;


/*
 * This file is part of the Meriel framework.
 *
 * (c) Stefano Anedda <dearste@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class Container implements \ArrayAccess {

    private $container = array();

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

}
