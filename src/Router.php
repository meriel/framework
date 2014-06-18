<?php

class Router {

    public $routes = array();

    public function get($uri, $action) {

        return $this->addRoute("GET", $uri, $action);
        /* $data = array();
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

          } */
    }

    public function post($uri, $action) {

        return $this->addRoute("POST", $uri, $action);
    }

    protected function addRoute($methods, $uri, $action) {
        return $this->add($this->createRoute($methods, $uri, $action));
    }

    protected function createRoute($methods, $uri, $action) {

        if ($this->routingToController($action)) {

            $action = $this->getControllerAction($action);
        }

        //var_dump($action);

        $route = new Routes($methods, $uri, $action);

        return $route;
    }

    protected function routingToController($action) {
        if (is_string($action) && is_array(explode("@", $action))) {
            return true;
        }
        return false;
    }

    protected function getControllerAction($action) {
        if (is_string($action))
            $action = array('uses' => $action);


        $action['controller'] = $action['uses'];

        $closure = $this->getClassClosure($action['uses']);

        return $action = array('uses' => $closure);
    }

    protected function getClassClosure($controller) {

        list($class, $method) = explode('@', $controller);

        $closure = new $class();

        return array( $closure, $method);
    }

    protected function add(Routes $route) {
        return $this->routes[] = $route;
    }

    public function dispatch(Request $request) {
        
    }

    public function getRoutes() {
        return $this->routes;
    }

}
