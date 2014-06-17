<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class Facade {

    protected static function getName()
    {
        throw new Exception('Facade does not implement getName method.');
    }

    public static function __callStatic($method, $args)
    {
        $instance = App::get(static::getName());

        if ( ! method_exists($instance, $method)) {
            throw new Exception(get_called_class() . ' does not implement ' . $method . ' method.');
        }

        return call_user_func_array(array( $instance, $method ), $args);
    }

}
