<?php

class Router {

    public static $routes = array();

    public function get($route, $controller) {

        //var_dump($route);

        $data = array();
        $req_url = isset($_GET['url']) ? "/" . rtrim($_GET['url'], '/') : '/';
        $req_met = $_SERVER['REQUEST_METHOD'];

        $pattern = "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', preg_quote($route)) . "$@D";



        $matches = array();
        // check if the current request matches the expression
        if ($req_met == "GET" && preg_match($pattern, $req_url, $matches)) {
            // remove the first match
            array_shift($matches);
            // call the callback with the matched positions as params
            if (is_callable($controller)) {

                $data = array(
                    "request" => $req_url,
                    "controller" => null,
                    "method" => null,
                    "data" => array(),
                    "callback" => $controller
                );
            } else if (is_string($controller)) {

                if (is_array($call = explode("@", $controller))) {

                    $data = array(
                        "request" => $req_url,
                        "controller" => $call[0],
                        "method" => $call[1],
                        "data" => $matches
                    );
                }
            }


            self::$routes = $data;

            return $this;

        }
    }

    public function post($route, $controller) {

        $data = array();
        $req_url = isset($_GET['url']) ? "/" . rtrim($_GET['url'], '/') : '/';
        $req_met = $_SERVER['REQUEST_METHOD'];

        $pattern = "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', preg_quote($route)) . "$@D";



        $matches = array();
        // check if the current request matches the expression
        if ($req_met == "POST" && preg_match($pattern, $req_url, $matches)) {
            // remove the first match
            array_shift($matches);
            // call the callback with the matched positions as params

            if (is_callable($controller)) {

                $data = array(
                    "request" => $req_url,
                    "controller" => null,
                    "method" => null,
                    "data" => array(),
                    "callback" => $controller
                );
            } else if (is_string($controller)) {

                if (is_array($call = explode("@", $controller))) {

                    $data = array(
                        "request" => $req_url,
                        "controller" => $call[0],
                        "method" => $call[1],
                        "data" => $matches
                    );
                }
            }
            self::$routes = $data;
            
            return $this;
        }
    }

}
