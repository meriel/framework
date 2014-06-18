<?php

class App {

    private static $instances = array();

    /**
     * Set object instance into $instances Array.
     *
     * @param  $name
     * @param  $instance
     * @return void
     */
    public static function set($name, $instance) {
        if (is_string($instance)) {
            $instance = new $instance();
        }

        static::$instances[$name] = $instance;
    }

    /**
     * Get an object instance from $instances Array.
     *
     * @param  $name
     * @return object
     */
    public static function get($name) {
        if (isset(static::$instances[$name])) {


            $instance = static::$instances[$name];

            if ($instance instanceof Closure) {
                $instance = $instance();
            }
        } else {
            self::set($name, ucfirst($name));

            $instance = static::$instances[$name];
        }

        return $instance;
    }

    public function run() {
        
    }

    public function __construct() {

        //ob_start();
        $dispatched = false;
        $routes = Route::getRoutes();

        foreach ($routes as $route) {


            try {
                $dispatched = $route->run();
                if ($dispatched) {
                    break;
                }
            } catch (Exception $e) {
                continue;
            }
        }

        if (!$dispatched) {
            $this->notFound();
        } else {



            Response::setContent($dispatched)->send();
        }
    }

    protected function notFound() {

        Response::setContent( View::make('404') )->send();
    }

}
