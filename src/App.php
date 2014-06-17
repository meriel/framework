<?php

class App {

    private static $instances = array();

    public static function set($name, $instance) {
        if (is_string($instance)) {
            $instance = new $instance();
        }

        static::$instances[$name] = $instance;
    }

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

    private $url_controller = null;
    private $url_action = null;
    private $url_params = array();

    public function __construct() {
        // create array with URL parts in $url


        if (!empty(Router::$routes)) {


            if (isset(Router::$routes['callback'])) {

                echo call_user_func(Router::$routes['callback']);
            } else if (file_exists('./app/controllers/' . Router::$routes['controller'] . '.php')) {

                require './app/controllers/' . Router::$routes['controller'] . '.php';

                $this->url_controller = new Router::$routes['controller']();

                if (method_exists($this->url_controller, Router::$routes['method'])) {

                    echo $this->url_controller->{Router::$routes['method']}(Router::$routes['data']);
                } else {

                    echo $this->url_controller->missingMethod(Router::$routes['data']);
                }
            }
        } else {

            $this->splitUrl();


            if (file_exists('./app/controllers/' . $this->url_controller . '.php')) {


                require './app/controllers/' . $this->url_controller . '.php';
                $this->url_controller = new $this->url_controller();

                if (method_exists($this->url_controller, $this->url_action)) {


                    if (!empty($this->url_params)) {
                        echo $this->url_controller->{$this->url_action}($this->url_params);
                    } else {
                        echo $this->url_controller->{$this->url_action}();
                    }
                } else {

                    $this->url_controller->missingMethod();
                }
            } else {
                // invalid URL, so simply show home/index
                require './app/controllers/HomeController.php';
                $home = new HomeController();
                echo $home->index();
            }
        }
    }

    /**
     * Get and split the URL
     */
    private function splitUrl() {
        if (isset($_GET['url'])) {

            // split URL
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);


            if ($url[0]) {
                $this->url_controller = $url[0];
                $this->url_action = (isset($url[1]) ? $url[1] : null);

                unset($url[0], $url[1]);
                $url = array_values($url);
                $this->url_params = $url;
            }
        }
    }

}
