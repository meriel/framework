<?php namespace Meriel\Support;

use ReflectionClass;

abstract class Services {

    protected $app;

    public function __construct($app) {
        $this->app = $app;
    }
    
    
    abstract public function register();

}
