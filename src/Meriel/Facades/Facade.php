<?php namespace Meriel\Facades;



abstract class Facade {

    protected static $app;
    protected static $resolvedInstance = array();

    public static function setFacadeApplication($app) {
        static::$app = $app;
    }

    protected static function getName() {
        throw new Exception('Facade does not implement getName method.');
    }

    protected static function getFacadeRoot() {
        $name = static::getName();

        if (is_object($name))
            return $name;

        if (isset(static::$resolvedInstance[$name])) {
            return static::$resolvedInstance[$name];
        }

        return static::$resolvedInstance[$name] = static::$app[$name];
    }
    
    public static function clearResolvedInstances(){
        static::$resolvedInstance = array();
    }

    public static function __callStatic($method, $args) {

        $instance = static::getFacadeRoot();
        
        //var_dump($instance);

        if (!method_exists($instance, $method)) {
            throw new \Exception(get_called_class() . ' does not implement ' . $method . ' method.');
        }


        switch (count($args)) {
            case 0:
                return $instance->$method();
            case 1:
                return $instance->$method($args[0]);
            case 2:
                return $instance->$method($args[0], $args[1]);
            case 3:
                return $instance->$method($args[0], $args[1], $args[2]);
            case 4:
                return $instance->$method($args[0], $args[1], $args[2], $args[3]);
            default:
                return call_user_func_array($instance->$method, $args);
        }
    }

}
