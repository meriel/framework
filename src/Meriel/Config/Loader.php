<?php namespace Meriel\Config;


use ArrayAccess;

class Loader implements ArrayAccess {

    protected $items = array();

    public function __construct($path, $group = null) {


        //$helpers = __DIR__ . '/../Support/helpers.php';

       // if (file_exists($helpers))
       //     require_once $helpers;

        $this->load($path, $group);

        return $this;
    }

    public function load($path, $group = null) {
        $files = glob("{$path}/*.php");
        foreach ($files as $file) {


            $key = preg_replace('/\\.[^.\\s]{3,4}$/', '', basename($file));

            $this[$key] = require $path . '/' . basename($file);
        }
    }

    public function get($key) {
        return $this[$key];
    }

    public function set($key, $value) {
        $this[$key] = $value;
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
