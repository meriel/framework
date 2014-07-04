<?php namespace Meriel\Database;

use ArrayAccess;

abstract class Model implements ArrayAccess{
    
    
    public function __construct(array $attributes = array())
    {}
}