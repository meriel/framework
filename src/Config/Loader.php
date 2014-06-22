<?php namespace Meriel\Config;

use ArrayAccess;

class Loader implements ArrayAccess {

    protected $items = array();

    public function __construct($path) {
        $this['config'] = array();
        echo "ok";
    }

    public function offsetExists($offset) {
        return array_key_exists($offset, $this->items);
    }

    public function offsetSet($offset, $value) {
        $this->items[$offset] = $value;
    }

    public function offsetUnset($offset) {
        if ($this->offsetExists($offset))
            unset($this->items[$offset]);
    }

    public function offsetGet($offset) {
        return $this->offsetExists($offset) ? $this->items[$offset] : NULL;
    }

}
