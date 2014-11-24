<?php namespace Meriel\Routing;

/*
 * This file is part of the Meriel framework.
 *
 * (c) Stefano Anedda <dearste@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Router {

    private $routes = array();
    
    public  $matchedRoutes = array();


    public function get($uri, $action) {

        return $this->addRoute("GET", $uri, $action);
        
    }

    public function post($uri, $action) {

        return $this->addRoute("POST", $uri, $action);
    }
    
    public function put($uri, $action) {

        return $this->addRoute("PUT", $uri, $action);
    }
    
    public function delete($uri, $action) {

        return $this->addRoute("DELETE", $uri, $action);
    }

    protected function addRoute($methods, $uri, $action) {
        return $this->add($this->createRoute($methods, $uri, $action));
    }

    protected function createRoute($methods, $uri, $action) {

        if ($this->routingToController($action)) {

            $action = $this->getControllerAction($action);
        }

        $route = new \Meriel\Routing\Routes($methods, $uri, $action);

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
            $action = array('fn' => $action);


        $action['controller'] = $action['fn'];

        $closure = $this->getClassClosure($action['fn']);

        return $action = array('fn' => $closure);
    }

    protected function getClassClosure($controller) {

        list($class, $method) = explode('@', $controller);

        $closure = new $class();
        
        if(!method_exists($closure, $method)){
           $method = "missingMethod";
        }

        return array($closure, $method);
    }

    protected function add(Routes $route) {
        return $this->routes[] = $route;
    }

    public function getRoutes() {
        
    
        $this->matchedRoutes = array();
        foreach ($this->routes as $route) {
            
             if($route->matches()){
                 $this->matchedRoutes[] = $route;
             }
             
        }
        
        return $this->matchedRoutes;
    }

    
    public function controllers(array $controllers) {
        foreach ($controllers as $uri => $name) {
            $this->controller($uri, $name);
        }
    }

    public function controller($uri, $controller, $names = array()) {}


}