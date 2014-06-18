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
        }else{
            self::set($name,  ucfirst($name) );

            $instance = static::$instances[$name];
        }

        return $instance;
    }

    public function run(){}



    public function __construct() {
        $routes = Route::getRoutes();

        if($routes){


            foreach($routes as $route){
                if($route->matches()){
                   echo $route->run();
                }
            }

        }else{
            //controller
        }
    }

}
